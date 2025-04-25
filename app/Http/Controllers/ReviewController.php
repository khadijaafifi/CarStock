<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Car;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    // Afficher les avis pour une voiture donnée
    public function show($carId)
    {
        // Récupérer la voiture et ses avis
        $car = Car::with('reviews')->find($carId);

        if (!$car) {
            return response()->json(['message' => 'Voiture non trouvée'], 404);
        }

        return response()->json([
            'car' => $car,
            'reviews' => $car->reviews
        ]);
    }

    // Ajouter un nouvel avis pour une voiture
    public function store(Request $request)
    {
        $request->validate([
            'car_id' => 'required|exists:cars,id',
            'rating' => 'required|integer|between:1,5',
            'session_id' => 'required|string'
        ]);

        $carId = $request->input('car_id');
        $rating = $request->input('rating');
        $sessionId = $request->input('session_id');

        // Vérifier si un avis existe déjà pour cette voiture et cette session
        $exists = Review::where('car_id', $carId)->where('session_id', $sessionId)->exists();
        if ($exists) {
            return response()->json(['message' => 'Vous avez déjà donné un avis pour cette voiture'], 400);
        }

        // Créer un nouvel avis
        $review = Review::create([
            'car_id' => $carId,
            'rating' => $rating,
            'session_id' => $sessionId
        ]);

        // Mettre à jour la note de la voiture
        $this->updateCarRating($carId);

        return response()->json([
            'message' => 'Avis ajouté avec succès',
            'review' => $review
        ]);
    }

    // Mettre à jour la note d'une voiture après un nouvel avis
    private function updateCarRating($carId)
    {
        $car = Car::find($carId);

        if (!$car) {
            Log::error("Voiture non trouvée lors de la mise à jour de la note", ['car_id' => $carId]);
            return;
        }

        // Calculer la nouvelle note de la voiture
        $reviews = $car->reviews;
        $totalRating = $reviews->sum('rating');
        $ratingCount = $reviews->count();

        if ($ratingCount > 0) {
            $newRating = $totalRating / $ratingCount;
            $car->update(['rating' => round($newRating, 1)]);
        }
    }

    // Supprimer un avis
    public function destroy($id)
    {
        $review = Review::find($id);

        if (!$review) {
            return response()->json(['message' => 'Avis non trouvé'], 404);
        }

        // Supprimer l'avis
        $review->delete();

        // Recalculer la note de la voiture après la suppression de l'avis
        $this->updateCarRating($review->car_id);

        return response()->json(['message' => 'Avis supprimé avec succès']);
    }
}
