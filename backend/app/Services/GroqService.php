<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GroqService
{
    private string $apiKey;
    private string $baseUrl;
    private string $model;
    private float  $temperature;
    private int    $maxTokens;
    private string $systemPrompt;
    private string $knowledgeBase;

    public function __construct()
    {
        $this->apiKey       = config('groq.api_key', '');
        $this->baseUrl      = config('groq.base_url', 'https://api.groq.com/openai/v1');
        $this->model        = config('groq.model', 'llama-3.3-70b-versatile');
        $this->temperature  = (float) config('groq.temperature', 0.3);
        $this->maxTokens    = (int) config('groq.max_tokens', 1024);
        $this->systemPrompt = config('groq.system_prompt', '');

        // Charger la base de connaissances
        $knowledgePath = storage_path('app/knowledge/topidealspace_knowledge.md');
        $this->knowledgeBase = file_exists($knowledgePath)
            ? file_get_contents($knowledgePath)
            : '';
    }

    /**
     * Construit le prompt système complet avec la base de connaissances.
     */
    private function buildSystemPrompt(): string
    {
        $base = $this->systemPrompt;

        if ($this->knowledgeBase !== '') {
            $base .= "\n\n---\n\nVoici la documentation complète de la plateforme que tu dois utiliser comme référence :\n\n"
                   . $this->knowledgeBase;
        }

        return $base;
    }

    /**
     * Génère une réponse en streaming (Server-Sent Events).
     * Retourne une StreamedResponse prête à être envoyée.
     */
    public function streamChat(
        string $question,
        ?int   $clientId  = null,
        string $sessionId = '',
        string $ipAddress = ''
    ): StreamedResponse {
        $startTime = microtime(true);

        // --- Vérification du cache -----------------------------------------
        $cacheKey = $this->buildCacheKey($question);
        if (config('groq.cache.enabled', true)) {
            $cached = Cache::get($cacheKey);
            if ($cached !== null) {
                // Loguer le hit de cache
                $this->logInteraction(
                    question:     $question,
                    answer:       $cached,
                    clientId:     $clientId,
                    sessionId:    $sessionId,
                    ipAddress:    $ipAddress,
                    cacheHit:     true,
                    responseTime: (int) ((microtime(true) - $startTime) * 1000),
                );

                // Simuler un streaming depuis le cache
                return $this->streamCachedResponse($cached);
            }
        }

        // --- Appel Groq API ------------------------------------------------
        $messages = [
            ['role' => 'system',  'content' => $this->buildSystemPrompt()],
            ['role' => 'user',    'content' => $question],
        ];

        $fullAnswer      = '';
        $promptTokens    = 0;
        $completionTokens = 0;

        $response = new StreamedResponse(function () use (
            $messages,
            $cacheKey,
            $question,
            $clientId,
            $sessionId,
            $ipAddress,
            $startTime,
            &$fullAnswer,
            &$promptTokens,
            &$completionTokens
        ) {
            // ── Vider TOUS les niveaux de buffer output PHP/Laravel ──────────
            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            try {
                // ── Streaming via fopen/fgets (plus fiable que CURLOPT_WRITEFUNCTION) ──
                $payload = json_encode([
                    'model'       => $this->model,
                    'messages'    => $messages,
                    'temperature' => $this->temperature,
                    'max_tokens'  => $this->maxTokens,
                    'stream'      => true,
                ]);

                $context = stream_context_create([
                    'http' => [
                        'method'        => 'POST',
                        'header'        => implode("\r\n", [
                            'Authorization: Bearer ' . $this->apiKey,
                            'Content-Type: application/json',
                            'Accept: text/event-stream',
                            'Content-Length: ' . strlen($payload),
                        ]),
                        'content'       => $payload,
                        'timeout'       => 90,
                        'ignore_errors' => true,
                    ],
                    'ssl' => [
                        'verify_peer'      => true,
                        'verify_peer_name' => true,
                    ],
                ]);

                $stream = @fopen($this->baseUrl . '/chat/completions', 'r', false, $context);

                if ($stream === false) {
                    echo 'data: ' . json_encode(['error' => 'Impossible de joindre le service IA. Vérifiez votre connexion.']) . "\n\n";
                    echo "data: [DONE]\n\n";
                    flush();
                    return;
                }

                // Lire ligne par ligne — fgets attend la fin d'une ligne (\n)
                while (!feof($stream)) {
                    $rawLine = fgets($stream);
                    if ($rawLine === false) {
                        break;
                    }

                    $line = trim($rawLine);

                    if ($line === '') {
                        continue;
                    }

                    if ($line === 'data: [DONE]') {
                        echo "data: [DONE]\n\n";
                        flush();
                        break;
                    }

                    if (!str_starts_with($line, 'data: ')) {
                        continue;
                    }

                    $json = substr($line, 6);
                    if (trim($json) === '' || trim($json) === '[DONE]') {
                        continue;
                    }

                    $parsed = json_decode($json, true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        continue; // JSON incomplet → ignorer
                    }

                    // Erreur renvoyée par l'API Groq
                    if (isset($parsed['error'])) {
                        $msg = $parsed['error']['message'] ?? 'Erreur API Groq';
                        echo 'data: ' . json_encode(['error' => $msg]) . "\n\n";
                        flush();
                        continue;
                    }

                    // Token de contenu
                    $chunk = $parsed['choices'][0]['delta']['content'] ?? null;
                    if ($chunk !== null && $chunk !== '') {
                        $fullAnswer .= $chunk;
                        echo 'data: ' . json_encode(['content' => $chunk], JSON_UNESCAPED_UNICODE) . "\n\n";
                        flush();
                    }
                }

                fclose($stream);

            } catch (\Throwable $e) {
                Log::channel('daily')->error('[GroqService] Exception: ' . $e->getMessage());
                echo 'data: ' . json_encode(['error' => 'Une erreur est survenue. Veuillez réessayer.']) . "\n\n";
                echo "data: [DONE]\n\n";
                flush();
            }

            // --- Mise en cache de la réponse complète ----------------------
            if (config('groq.cache.enabled', true) && $fullAnswer !== '') {
                Cache::put(
                    $cacheKey,
                    $fullAnswer,
                    config('groq.cache.ttl', 1800)
                );
            }

            // --- Log de l'interaction --------------------------------------
            $this->logInteraction(
                question:     $question,
                answer:       $fullAnswer,
                clientId:     $clientId,
                sessionId:    $sessionId,
                ipAddress:    $ipAddress,
                cacheHit:     false,
                responseTime: (int) ((microtime(true) - $startTime) * 1000),
            );
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('X-Accel-Buffering', 'no');
        $response->headers->set('Connection', 'keep-alive');

        return $response;
    }

    /**
     * Simule un streaming depuis le cache (envoi mot par mot).
     */
    private function streamCachedResponse(string $content): StreamedResponse
    {
        $response = new StreamedResponse(function () use ($content) {
            // Vider TOUS les niveaux de buffer
            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            // Découper en mots/tokens pour simuler le streaming
            $words = preg_split('/(\s+)/u', $content, -1, PREG_SPLIT_DELIM_CAPTURE);
            foreach ($words as $word) {
                if ($word === '') {
                    continue;
                }
                echo 'data: ' . json_encode(['content' => $word, 'cached' => true], JSON_UNESCAPED_UNICODE) . "\n\n";
                flush();
                usleep(10000); // 10ms entre chaque mot
            }

            echo "data: [DONE]\n\n";
            flush();
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('X-Accel-Buffering', 'no');
        $response->headers->set('Connection', 'keep-alive');

        return $response;
    }

    /**
     * Génère une clé de cache déterministe pour une question.
     */
    private function buildCacheKey(string $question): string
    {
        $prefix = config('groq.cache.prefix', 'ai_chat_');
        // Normaliser la question avant de hasher (minuscule, trim, espaces normalisés)
        $normalized = preg_replace('/\s+/', ' ', mb_strtolower(trim($question)));
        return $prefix . md5($normalized);
    }

    /**
     * Enregistre l'interaction dans la base de données et les logs.
     */
    private function logInteraction(
        string  $question,
        string  $answer,
        ?int    $clientId,
        string  $sessionId,
        string  $ipAddress,
        bool    $cacheHit,
        int     $responseTime
    ): void {
        if (!config('groq.logging.enabled', true)) {
            return;
        }

        // Log fichier uniquement (la table DB est désactivée — trop volumineuse avec de nombreux utilisateurs)
        Log::channel(config('groq.logging.channel', 'daily'))->info('[AI_ASSISTANT]', [
            'question'      => mb_substr($question, 0, 300),
            'answer_length' => mb_strlen($answer),
            'cache_hit'     => $cacheHit,
            'response_ms'   => $responseTime,
            'client_id'     => $clientId,
            'ip'            => $ipAddress,
        ]);
    }
}
