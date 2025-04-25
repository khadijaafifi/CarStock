<?php

namespace App\Services;

use App\Models\ChatHistory;
use Illuminate\Support\Facades\Log;

class ChatHistoryService
{
    public function save($sessionId, $leadMessage, $assistantResponse, $meta = [])
    {
        try {
            Log::debug('Attempting to save chat history', [
                'session_id' => $sessionId,
                'message_length' => strlen($leadMessage),
                'response_length' => strlen($assistantResponse)
            ]);

            $history = ChatHistory::updateOrCreate([
                'session_id' => $sessionId,
                'lead_message' => $leadMessage,
                'assistant_response' => $assistantResponse,
                'meta_data' => $meta,
                'created_at' => now(),
            ]);

            if (!$history) {
                Log::error('Chat history creation failed silently', [
                    'session_id' => $sessionId
                ]);
                return false;
            }

            Log::debug('Chat history saved successfully', [
                'id' => $history->id,
                'session_id' => $sessionId
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to save chat history', [
                'error' => $e->getMessage(),
                'session_id' => $sessionId,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    public function getBySession($sessionId)
    {
        $entries = ChatHistory::where('session_id', $sessionId)
            ->orderBy('created_at')
            ->get();

        $history = [];

        foreach ($entries as $entry) {
            $history[] = [
                'role' => 'user',
                'content' => $entry->lead_message
            ];
            $history[] = [
                'role' => 'assistant',
                'content' => $entry->assistant_response
            ];
        }

        return $history;
    }
}
