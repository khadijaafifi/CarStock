<?php
namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
 use Illuminate\Support\Facades\Auth;
class CarController extends Controller
{
    // Afficher toutes les voitures
    public function index()
    {
        $cars = Car::all();
        return view('cars.index', compact('cars'));
    }

    // Afficher le formulaire de création
    public function create()
    {
        return view('cars.create');
    }

    // Enregistrer une nouvelle voiture
    public function store(Request $request)
    {
        $request->validate([
            'marque' => 'required|string|max:255',
            'couleur' => 'required|string|max:255',
            'modele' => 'nullable|string|max:255',
            'annee' => 'nullable|integer|digits:4',
            'prix' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('car_images', 'public');
        }

        Car::create([
            'marque' => $request->marque,
            'modele' => $request->modele,
            'couleur' => $request->couleur,
            'annee' => $request->annee,
            'prix' => $request->prix,
            'description' => $request->description,
            'image' => $imagePath,
        ]);

        return redirect()->route('cars.index')->with('success', 'Voiture ajoutée avec succès.');
    }

    // Afficher le formulaire d'édition
    public function edit($id)
    {
        $car = Car::findOrFail($id);
        return view('cars.edit', compact('car'));
    }

    // Mettre à jour une voiture
    public function update(Request $request, $id)
    {
        $request->validate([
            'marque' => 'required|string|max:255',
            'couleur' => 'required|string|max:255',
            'modele' => 'nullable|string|max:255',
            'annee' => 'nullable|integer|digits:4',
            'prix' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $car = Car::findOrFail($id);

        if ($request->hasFile('image')) {
            if ($car->image) {
                Storage::disk('public')->delete($car->image);
            }
            $car->image = $request->file('image')->store('car_images', 'public');
        }

        $car->update([
            'marque' => $request->marque,
            'couleur' => $request->couleur,
            'modele' => $request->modele,
            'annee' => $request->annee,
            'prix' => $request->prix,
            'description' => $request->description,
        ]);

        return redirect()->route('cars.index')->with('success', 'Voiture mise à jour avec succès.');
    }

    // Supprimer une voiture
    public function destroy($id)
    {
        $car = Car::findOrFail($id);

        if ($car->image) {
            Storage::disk('public')->delete($car->image);
        }

        $car->delete();

        return redirect()->route('cars.index')->with('success', 'Voiture supprimée avec succès.');
    }

    // Tableau de bord admin

public function dashboard()
{
    // Vérification plus flexible pour tous les admins
    if (!Auth::check() || !str_ends_with(Auth::user()->email, '@admin.com')) {
        return redirect('/')->with('error', 'Accès réservé aux administrateurs');
    }

    $cars = Car::paginate(10);
    return view('dashboard', compact('cars'));
}
public function show($id)
{
    $car = Car::findOrFail($id);
    return view('cars.show', compact('car'));
}
// public function rateCar(Request $request, Car $car)
// {
//     $request->validate(['rating' => 'required|integer|between:1,5']);
    
//     $car->reviews()->create([
//         'rating' => $request->rating,
//         'session_id' => session()->getId() // Stocke l'ID de session
//     ]);
//     // Calcul avec arrondi supérieur
//     $average = $car->reviews()->avg('rating');
//     $car->rating = ceil($average);
//     $car->save();

//     return back()->with('success', 'Merci pour votre note !');
// }
// public function search(Request $request)
// {
//     $query = $request->input('query');

//     if (!$query) {
//         return redirect()->back()->with('error', 'Veuillez entrer une recherche.');
//     }

//     // Chercher les voitures en fonction de la requête
//     $cars = Car::where('marque', 'like', '%' . $query . '%')
//         ->orWhere('modele', 'like', '%' . $query . '%')
//         ->orWhere('couleur', 'like', '%' . $query . '%')
//         ->get();

//     // Obtenir une réponse de l'IA
//     $aiResponse = $this->getAiResponse("Recherche pour : " . $query);

//     // Passer les résultats à la vue avec la réponse de l'IA
//     return view('cars.search-results', compact('cars', 'query', 'aiResponse'));
// }


// public function getAiResponse(Request $request)
// {
//     $message = $request->input('message');

//     // Appel à l'API OpenAI pour générer une réponse
//     $aiResponse = $this->getAiResponse($message);  // Fonction que tu utilises pour interagir avec OpenAI

//     return response()->json(['response' => $aiResponse]);
// }

}
