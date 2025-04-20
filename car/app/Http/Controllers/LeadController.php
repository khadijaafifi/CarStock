<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\ChatHistory;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class LeadController extends Controller
{
    public function index()
    {
        // Récupérer tous les messages, on filtrera en PHP
        $sessions = ChatHistory::orderBy('created_at', 'desc')
            ->get()
            ->filter(function ($chat) {
                $meta = $chat->meta_data;

                return is_array($meta) && !empty($meta['recap_discussion']);
            })
            ->groupBy('session_id')
            ->map(function ($messages) {
                return $messages->first(); // le plus récent pour chaque session
            });

        foreach ($sessions as $chat) {
            $meta = $chat->meta_data;

            // Vérifie qu'au moins une donnée est présente
            $hasData = !empty($meta['recap_discussion']);

            if ($hasData && !Lead::where('session_id', $chat->session_id)->exists()) {
                Log::info('Création lead pour session : ' . $chat->session_id);
                Lead::create([
                    'session_id' => $chat->session_id,
                    'name' => $meta['nom'] ?? null,
                    'email' => $meta['email'] ?? null,
                    'phone' => $meta['numero'] ?? null,
                    'summary' => $meta['recap_discussion'] ?? null,
                ]);
            }
        }

        // Charger uniquement les leads qui ont au moins un champ non vide
        $leads = Lead::where(function ($query) {
                $query->whereNotNull('name')
                      ->orWhereNotNull('email')
                      ->orWhereNotNull('phone')
                      ->orWhereNotNull('summary');
            })
            ->latest()
            ->paginate(10);

        return view('leads', compact('leads'));
    }

    public function destroy($id)
    {
        $lead = Lead::findOrFail($id);
        $lead->delete();

        return redirect()->back()->with('success', 'Lead supprimé avec succès.');
    }

    public function exportPdf()
    {
        $leads = Lead::latest()->get();

        $pdf = Pdf::loadView('pdf', compact('leads'));

        return $pdf->download('leads.pdf');
    }
}
