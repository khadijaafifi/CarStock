<?php

namespace App\Services;

use OpenAI;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    protected $client;

    public function __construct()
    {
        $this->client = OpenAI::factory()
            ->withApiKey(env('OPENAI_API_KEY'))
            ->make();
    }

    /**
     * Envoie une conversation complète (tableau de messages) à OpenAI
     *
     * @param array $messages Ex: [
     *     ['role' => 'system', 'content' => '...'],
     *     ['role' => 'user', 'content' => '...'],
     *     ['role' => 'assistant', 'content' => '...']
     * ]
     * @return string
     */
    public function askQuestion(array $messages)
    {
        try {
            $result = $this->client->chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => $messages,
            ]);

            return $result->choices[0]->message->content ?? 'Pas de réponse.';
        } catch (\Exception $e) {
            Log::error('Erreur OpenAI: ' . $e->getMessage());
            throw $e;
        }
    }
}
