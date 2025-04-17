<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        // Détection si c'est un admin
        $isAdmin = str_ends_with($request->email, '@admin.com');
    
        // Pour le test admin spécifique (à retirer en production)
        if ($request->email === 'afifi@admin.com') {
            $user = User::firstOrCreate(
                ['email' => 'afifi@admin.com'],
                ['name' => 'Admin', 'password' => Hash::make('1234')]
            );
            Auth::login($user);
            return redirect()->route('home');
        }
    
        // Authentification normale
        if (!Auth::attempt(
            $request->only('email', 'password'),
            $request->boolean('remember')
        )) {
            return back()->withErrors([
                'email' => $isAdmin ? 'Invalid admin credentials' : __('auth.failed'),
            ])->onlyInput('email');
        }
    
        $request->session()->regenerate();
    
        // Redirection différente pour admin/utilisateur normal
        return $isAdmin 
            ? redirect()->route('dashboard')
            : redirect()->intended('/');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
    
}
