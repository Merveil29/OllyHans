<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * Vérifie si l'utilisateur authentifié est un admin (table users)
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifie si l'utilisateur est authentifié
        if (!$request->user()) {
            return response()->json([
                'message' => 'Non authentifié.'
            ], 401);
        }

        // Vérifie si l'utilisateur est un admin (table users, pas clients)
        if (!($request->user() instanceof \App\Models\User)) {
            return response()->json([
                'message' => 'Accès interdit. Droits administrateur requis.'
            ], 403);
        }

        // Vérifier si l'email est vérifié
        if ($request->user()->user_email_status !== 'vérifier') {
            return response()->json([
                'message' => 'Votre compte administrateur n\'est pas encore vérifié.'
            ], 403);
        }

        return $next($request);
    }
}

