<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OpenAIService;
use App\Services\ChatHistoryService;
use App\Models\Car;
use Illuminate\Support\Facades\Log;
use App\Models\ChatHistory;
use App\Notifications\WelcomeEmail;
use Illuminate\Support\Facades\Notification;
use App\Services\OpelScraperService;

class SearchController extends Controller
{
    protected $openAI;
    protected $chatHistory;
    protected $opelScraper;

    public function __construct(OpenAIService $openAI, ChatHistoryService $chatHistory, OpelScraperService $opelScraper)
    {
        $this->openAI = $openAI;
        $this->chatHistory = $chatHistory;
        $this->opelScraper = $opelScraper;
    }

    public function getAiResponse(Request $request)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        $userQuestion = $request->input('message');
        $sessionId = $request->session()->getId();

        Log::info('New chat request', [
            'session_id' => $sessionId,
            'question' => $userQuestion
        ]);

        try {
             //Contexte voitures
            $opelModels = $this->opelScraper->getModels();
            $cars = Car::all();
            $localCarsText = "Voitures en stock (base de données):\n" . $cars->map(fn($car) =>
                "- {$car->marque} {$car->modele}, {$car->couleur}, {$car->prix} MAD"
            )->implode("\n");

            $opelModelsText = "Modèles Opel (site officiel) :\n" . implode("\n", array_map(fn($model) => "- $model", $opelModels));
            $carContext = "$localCarsText\n\n$opelModelsText";

            // Historique + nouveau message
            $history = $this->chatHistory->getBySession($sessionId);
            $historyWithContext = array_merge([[
                'role' => 'system',
                'content' => "$carContext\n\nTu es un assistant commercial spécialisé dans les voitures de marque Opel. Réponds de façon professionnelle et amicale dans la langue de l'utilisateur. Termine la conversation par une demande de contact (nom, email, téléphone)."
            ]], $history, [[
                'role' => 'user',
                'content' => $userQuestion
            ]]);

            Log::debug('Sending request to OpenAI', [
                'session_id' => $sessionId,
                'message_count' => count($historyWithContext)
            ]);

            // Réponse de l'assistant
            $response = $this->openAI->askQuestion($historyWithContext);

            Log::debug('Received response from OpenAI', [
                'session_id' => $sessionId,
                'response_length' => strlen($response)
            ]);

            // Sauvegarde sans meta_data
            $this->chatHistory->save(
                $sessionId,
                $userQuestion,
                $response,
                []
            );

            // Extraire infos personnelles après avoir tout sauvegardé
            $metaData = $this->AiextractData($request);

            // Mettre à jour le dernier message avec les meta_data
            $lastMessage = ChatHistory::where('session_id', $sessionId)
                ->latest()
                ->first();

            if ($lastMessage) {
                $lastMessage->update(['meta_data' => $metaData]);
            }

            return response()->json([
                'response' => $response,
                'status' => 'success',
                'session_id' => $sessionId
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getAiResponse', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'session_id' => $sessionId
            ]);

            $errorResponse = 'Désolé, une erreur est survenue. Veuillez réessayer plus tard.';

            try {
                $this->chatHistory->save(
                    $sessionId,
                    $userQuestion,
                    $errorResponse,
                    ['error' => $e->getMessage()]
                );
            } catch (\Exception $historyException) {
                Log::critical('Failed to save error to chat history', [
                    'original_error' => $e->getMessage(),
                    'history_error' => $historyException->getMessage()
                ]);
                $this->saveToFileFallback($sessionId, $userQuestion, $errorResponse);
            }

            return response()->json([
                'response' => $errorResponse,
                'status' => 'error',
                'session_id' => $sessionId
            ]);
        }
    }

    public function getChatHistory(Request $request)
    {
        $sessionId = $request->session()->getId();
        $history = $this->chatHistory->getBySession($sessionId);
        return response()->json($history);
    }

    public function checkServiceStatus()
    {
        try {
            $testSave = $this->chatHistory->save(
                'test-session',
                'Test question',
                'Test response',
                ['test' => true]
            );

            return response()->json([
                'database_working' => $testSave,
                'fallback_log_exists' => file_exists(storage_path('logs/chat_history_fallback.log')),
                'timestamp' => now()->toDateTimeString()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'status' => 'service_error'
            ], 500);
        }
    }

    private function saveToFileFallback($sessionId, $question, $response)
    {
        try {
            $logMessage = sprintf(
                "[%s] Session: %s\nQuestion: %s\nResponse: %s\n\n",
                now()->toDateTimeString(),
                $sessionId,
                $question,
                $response
            );

            file_put_contents(
                storage_path('logs/chat_history_fallback.log'),
                $logMessage,
                FILE_APPEND
            );

            Log::info('Saved chat history to fallback file', ['session_id' => $sessionId]);

        } catch (\Exception $e) {
            Log::emergency('Failed to save to fallback file', [
                'error' => $e->getMessage(),
                'session_id' => $sessionId
            ]);
        }
    }

    private function AiextractData($request)
    {
        $sessionId = $request->session()->getId();

        $messages = ChatHistory::where('session_id', $sessionId)
                               ->orderBy('created_at')
                               ->get();

        $conversation = '';
        foreach ($messages as $msg) {
            $conversation .= "Utilisateur : {$msg->lead_message}\n";
            $conversation .= "Assistant : {$msg->assistant_response}\n";
        }

        $prompt = "Voici la conversation complète entre un utilisateur et un assistant :\n\n$conversation\n\n" .
                  "Ta tâche est d'analyser cette discussion pour extraire les informations suivantes, uniquement si elles apparaissent clairement :\n" .
                  "- nom de l'utilisateur\n" .
                  "- adresse email\n" .
                  "- numéro de téléphone\n" .
                  "- un résumé clair de la discussion a la fin de discussion non pas a chaque message.\n\n" .
                  "IMPORTANT :\n" .
                  "- Si une information n'est pas présente, laisse le champ vide\n" .
                  "- Ne complète pas ou n'invente pas\n" .
                  "- Réponds uniquement au format JSON SANS ajout ni commentaire\n\n" .
                  "{\"nom\": \"\", \"email\": \"\", \"numero\": \"\", \"recap_discussion\": \"\"}";

        $response = $this->openAI->askQuestion([
            [
                'role' => 'system',
                'content' => "Tu es un assistant chargé d'extraire des données personnelles et de résumer chaque discussion à partir de texte. Réponds uniquement en JSON."
            ],
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ]);

        Log::debug('Réponse brute IA extraction', ['response' => $response]);

        $data = json_decode($response, true);

        return [
            'nom' => $data['nom'] ?? '',
            'email' => $data['email'] ?? '',
            'numero' => $data['numero'] ?? '',
            'recap_discussion' => $data['recap_discussion'] ?? ''
        ];
    }

    public function envoyerEmails()
    {
        $histories = ChatHistory::whereNotNull('meta_data')->get();

        foreach ($histories as $history) {
            $meta = $history->meta_data;
            $email = $meta['email'] ?? null;
            $name = $meta['nom'] ?? 'Client';

            if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Notification::route('mail', $email)
                    ->notify(new WelcomeEmail($name));
            }
        }

        return response()->json(['status' => 'Emails envoyés avec succès']);
    }

    public function resetSession(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerate();

        return response()->json([
            'status' => 'new_session_started',
            'session_id' => $request->session()->getId(),
            'message' => 'Nouvelle session démarrée avec succès.'
        ]);
    }
}
