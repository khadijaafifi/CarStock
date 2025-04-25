<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OpenAIService;
use App\Services\ChatHistoryService;
use App\Services\CarScraperService;
use App\Models\Site;
use App\Models\Car;
use App\Models\ChatHistory;
use App\Notifications\WelcomeEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SearchController extends Controller
{
    protected $openAI;
    protected $chatHistory;
    protected $carScraper;

    public function __construct(OpenAIService $openAI, ChatHistoryService $chatHistory, CarScraperService $carScraper)
    {
        $this->openAI = $openAI;
        $this->chatHistory = $chatHistory;
        $this->carScraper = $carScraper;
    }

    public function getAiResponse(Request $request)
    {
        $request->validate(['message' => 'required|string']);
        $userQuestion = $request->input('message');
        $sessionId = $request->session()->getId();

        Log::info('New chat request', ['session_id' => $sessionId, 'question' => $userQuestion]);

        try {
            $sites = Site::all();
            $brands = $sites->pluck('name')->toArray();
            // Contexte voitures
        $cars = Car::all();
        $carContext = "Voitures en stock:\n" . $cars->map(fn($car) =>
            "- {$car->marque} {$car->modele}, {$car->couleur}, {$car->prix} MAD"
        )->implode("\n");
            $carContext .= "Voici les modèles disponibles :\n\n";
            foreach ($brands as $brand) {
                $scraped = $this->carScraper->getVehicleModels($brand);
                if ($scraped['success']) {
                    $carContext .= "Marque: {$brand}\n";
                    foreach ($scraped['models'] as $model) {
                        $features = implode(', ', $model['features']);
                        $carContext .= "- {$model['name']} (Prix: {$model['price']}, Motorisation: {$model['motorization']}, Caractéristiques: {$features})\n";
                    }
                    $carContext .= "\n";
                } else {
                    $carContext .= "Marque: {$brand} (modèles indisponibles)\n\n";
                }
            }

            Log::debug('Contexte généré pour OpenAI', ['carContext' => $carContext]);

            $history = $this->chatHistory->getBySession($sessionId);

            $historyWithContext = array_merge([[
                'role' => 'system',
                'content' => "$carContext\n\nTu es un assistant commercial spécialisé dans les voitures listées ci-dessus. Réponds uniquement à partir de ces informations. À la fin de chaque discussion, demande les coordonnées personnelles (nom, email, numéro de téléphone) et un avis entre 1 et 5 sur les voitures discutées ou déjà utilisées."
            ]], $history, [[
                'role' => 'user',
                'content' => $userQuestion
            ]]);

            $response = $this->openAI->askQuestion($historyWithContext);
            Log::info('Réponse de OpenAI', ['response' => $response]);

            $this->chatHistory->save($sessionId, $userQuestion, $response, []);

            $metaData = $this->AiextractData($request);

            $lastMessage = ChatHistory::where('session_id', $sessionId)->latest()->first();
            if ($lastMessage) {
                $lastMessage->update(['meta_data' => $metaData]);
            }

            return response()->json([
                'response' => $response,
                'lead' => $metaData,
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
                $this->chatHistory->save($sessionId, $userQuestion, $errorResponse, ['error' => $e->getMessage()]);
            } catch (\Exception $historyException) {
                $this->saveToFileFallback($sessionId, $userQuestion, $errorResponse);
            }

            return response()->json([
                'response' => $errorResponse,
                'status' => 'error',
                'session_id' => $sessionId
            ]);
        }
    }

    private function AiextractData($request)
    {
        $sessionId = $request->session()->getId();
        $messages = ChatHistory::where('session_id', $sessionId)->orderBy('created_at')->get();

        $conversation = '';
        foreach ($messages as $msg) {
            $conversation .= "Utilisateur : {$msg->lead_message}\n";
            $conversation .= "Assistant : {$msg->assistant_response}\n";
        }

        $prompt = "Voici la conversation complète entre un utilisateur et un assistant :\n\n$conversation\n\n" .
                  "Ta tâche est d'analyser cette discussion pour extraire les informations suivantes, uniquement si elles apparaissent clairement :\n" .
                  "- nom de l'utilisateur\n- adresse email\n- numéro de téléphone\n- un résumé clair de la discussion à la fin\n- une note entre 1 et 5 si elle a été donnée\n\n" .
                  "IMPORTANT :\n- Si une information n'est pas présente, laisse le champ vide\n- Ne complète pas ou n'invente pas\n- Réponds uniquement au format JSON strict comme ceci :\n\n" .
                  "{\"nom\": \"\", \"email\": \"\", \"numero\": \"\", \"recap_discussion\": \"\", \"rating\": \"\"}";

        $response = $this->openAI->askQuestion([
            ['role' => 'system', 'content' => "Tu es un assistant qui extrait des données de discussion et fournit un résumé. Réponds uniquement en JSON."],
            ['role' => 'user', 'content' => $prompt]
        ]);

        $data = json_decode($response, true);

        return [
            'nom' => $data['nom'] ?? '',
            'email' => $data['email'] ?? '',
            'numero' => $data['numero'] ?? '',
            'recap_discussion' => $data['recap_discussion'] ?? '',
            'rating' => $data['rating'] ?? ''
        ];
    }

    private function saveToFileFallback($sessionId, $question, $response)
    {
        $logMessage = sprintf("[%s] Session: %s\nQuestion: %s\nResponse: %s\n\n", now(), $sessionId, $question, $response);
        file_put_contents(storage_path('logs/chat_history_fallback.log'), $logMessage, FILE_APPEND);
        Log::info('Saved to fallback file', ['session_id' => $sessionId]);
    }

    public function getChatHistory(Request $request)
    {
        $sessionId = $request->session()->getId();
        return response()->json($this->chatHistory->getBySession($sessionId));
    }

    public function checkServiceStatus()
    {
        try {
            $test = $this->chatHistory->save('test-session', 'test', 'response', ['test' => true]);
            return response()->json([
                'database_working' => $test,
                'fallback_log_exists' => file_exists(storage_path('logs/chat_history_fallback.log')),
                'timestamp' => now()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    public function envoyerEmails()
    {
        $histories = ChatHistory::whereNotNull('meta_data')->get();

        foreach ($histories as $history) {
            $meta = $history->meta_data;
            $email = $meta['email'] ?? null;
            if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Notification::route('mail', $email)->notify(new WelcomeEmail($meta));
            }
        }

        return response()->json(['status' => 'emails sent']);
    }
}
