<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GroqService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use OpenApi\Annotations as OA;

class AiAssistantController extends Controller
{
    public function __construct(private readonly GroqService $groqService)
    {
    }

    /**
     * POST /api/v1/ai/chat
     *
     * Reçoit la question de l'utilisateur et retourne une réponse en streaming (SSE).
     * Rate limit : 10 req/min (configuré via throttle:ai dans les routes).
     * Cache Redis 30 min pour les questions répétitives.
     * Logging complet (fichier + BDD).
     * 
     * @OA\Post(
     *     path="/ai/chat",
     *     summary="Chat avec l'assistant IA",
     *     description="Envoie une question à l'assistant IA et reçoit une réponse en streaming (SSE). Rate limit: 10 req/min.",
     *     operationId="aiChat",
     *     tags={"Assistant IA"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"question"},
     *             @OA\Property(property="question", type="string", minLength=3, maxLength=1000, description="La question à poser"),
     *             @OA\Property(property="session_id", type="string", maxLength=64, description="ID de session optionnel pour le contexte")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Réponse en streaming (Server-Sent Events)",
     *         @OA\MediaType(mediaType="text/event-stream")
     *     ),
     *     @OA\Response(response=422, description="Erreur de validation"),
     *     @OA\Response(response=429, description="Trop de requêtes (rate limit)")
     * )
     */
    public function chat(Request $request): StreamedResponse
    {
        // --- Validation -------------------------------------------------------
        $validated = $request->validate([
            'question'   => ['required', 'string', 'min:3', 'max:1000'],
            'session_id' => ['nullable', 'string', 'max:64'],
        ]);

        $question  = trim($validated['question']);
        $sessionId = $validated['session_id'] ?? $request->header('X-Session-ID', Str::uuid()->toString());
        $ipAddress = $request->ip() ?? '';

        // Résoudre l'ID client si authentifié
        $clientId = null;
        if ($request->user()) {
            // L'utilisateur authentifié via Sanctum
            $clientId = $request->user()->id_client ?? $request->user()->id ?? null;
        }

        // --- Streaming --------------------------------------------------------
        return $this->groqService->streamChat(
            question:  $question,
            clientId:  $clientId,
            sessionId: $sessionId,
            ipAddress: $ipAddress,
        );
    }

    /**
     * GET /api/v1/ai/health
     *
     * Vérifie que l'API Groq est configurée.
     * 
     * @OA\Get(
     *     path="/ai/health",
     *     summary="État de santé de l'assistant IA",
     *     description="Vérifie que l'API Groq est correctement configurée",
     *     operationId="aiHealth",
     *     tags={"Assistant IA"},
     *     @OA\Response(
     *         response=200,
     *         description="État de l'API",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", enum={"ok", "misconfigured"}),
     *             @OA\Property(property="model", type="string", example="llama-3.3-70b-versatile"),
     *             @OA\Property(property="cache", type="string", example="enabled (30min)")
     *         )
     *     )
     * )
     */
    public function health(): \Illuminate\Http\JsonResponse
    {
        $apiKey = config('groq.api_key');

        return response()->json([
            'status' => $apiKey ? 'ok' : 'misconfigured',
            'model'  => config('groq.model'),
            'cache'  => config('groq.cache.enabled') ? 'enabled (' . (config('groq.cache.ttl') / 60) . 'min)' : 'disabled',
        ]);
    }
}
