<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\ChatHistory;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $sessionId = $request->session()->getId();

        // Récupérer tous les messages pour cette session
        $messages = ChatHistory::where('session_id', $sessionId)
            ->orderBy('created_at')
            ->get();

        // Vérifier s'il y a au moins un message d'utilisateur
        $userMessage = $messages->first();

        if ($userMessage && !$this->leadAlreadyExists($sessionId)) {
            $lastMsg = $messages->last();
            $meta = $lastMsg->meta_data ?? [];

            Lead::create([
                'session_id' => $sessionId,
                'name' => $meta['nom'] ?? null,
                'email' => $meta['email'] ?? null,
                'phone' => $meta['numero'] ?? null,
                'summary' => $meta['recap_discussion'],
            ]);
        }

        // Récupérer tous les leads paginés (même incomplets)
        $leads = Lead::latest()->paginate(10);

        // Construire la conversation
        $conversation = '';
        foreach ($messages as $msg) {
            $conversation .= "Utilisateur : {$msg->lead_message}\n";
            $conversation .= "Assistant : {$msg->assistant_response}\n";
        }

        return view("leads", compact('conversation', 'leads'));
    }

    public function destroy($id)
    {
        $lead = Lead::findOrFail($id);
        $lead->delete();

        return redirect()->back()->with('success', 'Lead supprimé avec succès.');
    }

    private function leadAlreadyExists($sessionId)
    {
        return Lead::where('session_id', $sessionId)->exists();
    }
}
