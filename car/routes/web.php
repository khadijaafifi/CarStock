<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\log;
use Illuminate\Support\Facades\Mail;


Route::resource('cars', CarController::class);

// Routes publiques
Route::get('/', [CarController::class, 'index'])->name('home');
Route::get('/cars/{id}', [CarController::class, 'show'])->name('cars.show');

// Chat routes
Route::get('/chat', [SearchController::class, 'show'])->name('search');
Route::post('/chat/response', [SearchController::class, 'getAiResponse'])->name('chat.response');
Route::get('/chat/reset', function() {
    session()->forget('chat_history');
    return response()->json(['status' => 'success']);
})->name('chat.reset');
// Routes protégées
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('cars', CarController::class)->except(['show']);
    Route::get('/dashboard', [CarController::class, 'dashboard'])->name('dashboard');
   
    Route::get('/cars/create', [CarController::class, 'create'])->name('cars.create');
    Route::post('/cars', [CarController::class, 'store'])->name('cars.store');
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');
    Route::delete('/leads/{id}', [LeadController::class, 'destroy'])->name('leads.destroy');
    Route::get('/leads/pdf', [LeadController::class, 'exportPdf'])->name('leads.export.pdf');

});
Route::match(['get', 'post'], '/get-ai-response', [SearchController::class, 'getAiResponse'])->middleware('web');
//  Route::get('/check-rating/{carId}', [ReviewController::class, 'checkAndProcessRating']);
Route::prefix('cars')->group(function () {
    // Afficher les avis pour un véhicule
    Route::get('{carId}/reviews', [ReviewController::class, 'show']);

    // Créer un nouvel avis pour un véhicule
    Route::post('{carId}/reviews', [ReviewController::class, 'create']);
});

// Route::get('/test-email', function () {
//     try {
//         Mail::raw('Ceci est un test d’email Laravel.', function ($message) {
//             $message->to('kenzaaddi05@gmail.com') // ➜ mets ici ton adresse pour tester
//                     ->subject('Test Email Laravel');
//         });

//         return 'Email envoyé avec succès !';
//     } catch (\Exception $e) {
//         Log::error('Erreur envoi email : ' . $e->getMessage());
//         return 'Échec envoi email.';
//     }
// });
// Route::get('/envoyer-mails', [SearchController::class, 'envoyerEmails']);

Route::get('/reset-session', [SearchController::class, 'resetSession'])->name('reset.session');

// Route::post('/cars/{car}/rate', [CarController::class, 'rateCar'])->name('cars.rate');
Route::post('/api/cars/{car}/review-from-chat', [ReviewController::class, 'reviewFromPrompt']);
Route::post('/cars/{car}/rate', [ReviewController::class, 'reviewFromPrompt'])->name('cars.rate');
Route::resource('sites', SiteController::class)->only(['index', 'create', 'store', 'destroy']);
// Route::get('/auto-review/{carId}', [ReviewController::class, 'checkAndProcessRating']);

require __DIR__.'/auth.php';