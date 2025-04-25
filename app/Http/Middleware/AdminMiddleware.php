<?php


namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // ✅ Import nécessaire

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user || !str_ends_with($user->email, '@admin.com')) {
            return redirect('/')->with('error', 'Accès réservé aux administrateurs');
        }

        return $next($request);
    }
}

