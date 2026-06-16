<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ajoute les headers de sécurité HTTP à chaque réponse.
 * Protège contre XSS, clickjacking, sniffing MIME, etc.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Ne pas modifier les réponses SSE (streaming)
        if ($response->headers->get('Content-Type') === 'text/event-stream') {
            return $response;
        }

        // Ne pas appliquer le CSP strict aux routes Swagger UI
        // (le script inline de l5-swagger n'est pas sous notre contrôle)
        $isSwaggerRoute = $request->is('api/documentation', 'api/oauth2-callback', 'docs', 'docs/*');

        // Protection contre le clickjacking
        $response->headers->set('X-Frame-Options', 'DENY');

        // Empêche le navigateur de deviner le type MIME
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Politique de référent stricte
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Empêche l'ouverture de fenêtres avec accès à window.opener
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');

        // CSP : relâché pour Swagger UI, strict pour le reste
        if ($isSwaggerRoute) {
            $csp = implode('; ', [
                "default-src 'self'",
                "script-src 'self' 'unsafe-inline'",        // requis par le script inline de swagger-ui
                "style-src 'self' 'unsafe-inline'",
                "img-src 'self' data: blob:",
                "font-src 'self'",
                "connect-src 'self' http://localhost:8000",
                "frame-ancestors 'none'",
                "base-uri 'self'",
            ]);
        } else {
            $csp = implode('; ', [
                "default-src 'self'",
                "script-src 'self'",
                "style-src 'self' 'unsafe-inline'",            // Tailwind inline styles
                "img-src 'self' data: https://res.cloudinary.com https://via.placeholder.com",
                "font-src 'self'",
                "connect-src 'self' https://api.groq.com",
                "frame-ancestors 'none'",
                "base-uri 'self'",
                "form-action 'self'",
            ]);
        }
        $response->headers->set('Content-Security-Policy', $csp);

        // Permissions Policy — désactive les API sensibles non utilisées
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        return $response;
    }
}
