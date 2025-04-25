<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function ceil;
class Car extends Model
{
    use HasFactory;

    protected $fillable = ['marque', 'modele', 'annee', 'couleur','prix' ,'description', 'image'];
    public function reviews()
{
    return $this->hasMany(Review::class);
}

// Méthode pour mettre à jour le rating (arrondi supérieur)
public function updateRating()
{
    $reviews = $this->reviews;  // Assuming the relationship is already set up
    $totalRating = $reviews->sum('rating');
    $reviewCount = $reviews->count();

    // Si des avis existent, calculer la note moyenne
    if ($reviewCount > 0) {
        $this->rating = ceil($totalRating / $reviewCount); // Calcul de la note moyenne, arrondi vers le haut
    } else {
        $this->rating = 0; // Aucun avis, note à 0 (ou une autre valeur par défaut)
    }
    
    // Mise à jour de la note du véhicule
    $this->save();
}
    
}
