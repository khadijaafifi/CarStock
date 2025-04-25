<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\ChatHistory;
use App\Models\Car;
use App\Models\Review;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class LeadController extends Controller
{
    public function index()
    {
        // Debug : log des 5 derniers meta_data
        foreach (ChatHistory::latest()->take(5)->get() as $chat) {
            $meta = is_array($chat->meta_data) ? $chat->meta_data : json_decode($chat->meta_data, true);
            Log::debug('Chat meta_data:', $meta ?? []);
        }

        // Récupérer les derniers messages de chaque session contenant un récapitulatif
        $sessions = ChatHistory::orderBy('created_at')
            ->get()
            ->filter(function ($chat) {
                $meta = is_array($chat->meta_data) ? $chat->meta_data : json_decode($chat->meta_data, true);
                return is_array($meta) && !empty($meta['recap_discussion']);
            })
            ->groupBy('session_id')
            ->map(fn($messages) => $messages->last());

        foreach ($sessions as $chat) {
            $meta = is_array($chat->meta_data) ? $chat->meta_data : json_decode($chat->meta_data, true);

            $hasData = !empty($meta['recap_discussion']);
            $hasRating = isset($meta['car_id'], $meta['rating']);

            // Création du lead s’il n’existe pas déjà
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

            // Création d’un avis si rating + car_id présents
            if ($hasRating) {
                $car = Car::find($meta['car_id']);
                if ($car) {
                    Review::create([
                        'car_id' => $meta['car_id'],
                        'rating' => $meta['rating'],
                        'voteurs' => 1, // important si tu utilises "voteurs" dans la moyenne pondérée
                    ]);
                    Log::info("Rating de {$meta['rating']} enregistré pour la voiture ID: {$meta['car_id']}");

                    // Mise à jour via ta méthode dédiée
                    $car->updateRating();
                } else {
                    Log::warning("Voiture non trouvée (ID: {$meta['car_id']})");
                }
            }
        }

        // Affichage des leads valides
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

        return $pdf->stream('leads.pdf');
    }
}
