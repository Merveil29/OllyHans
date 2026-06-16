<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Réponse succès standardisée
     */
    protected function success(mixed $data = null, string $message = '', int $status = 200): JsonResponse
    {
        $response = ['success' => true];

        if ($message !== '') {
            $response['message'] = $message;
        }

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $status);
    }

    /**
     * Réponse succès avec méta pagination
     */
    protected function paginated(mixed $paginator, callable $mapper, string $message = ''): JsonResponse
    {
        $response = [
            'success' => true,
            'data'    => $paginator->getCollection()->map($mapper)->values(),
            'meta'    => [
                'total'        => $paginator->total(),
                'per_page'     => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'from'         => $paginator->firstItem(),
                'to'           => $paginator->lastItem(),
            ],
        ];

        if ($message !== '') {
            $response['message'] = $message;
        }

        return response()->json($response);
    }

    /**
     * Réponse erreur standardisée
     */
    protected function error(string $message, array $errors = [], int $status = 400): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }

    /**
     * Réponse 404
     */
    protected function notFound(string $message = 'Ressource introuvable.'): JsonResponse
    {
        return $this->error($message, [], 404);
    }

    /**
     * Réponse 403
     */
    protected function forbidden(string $message = 'Accès refusé.'): JsonResponse
    {
        return $this->error($message, [], 403);
    }

    /**
     * Réponse erreur serveur (log + masquage en prod)
     */
    protected function serverError(\Throwable $e, string $message = 'Une erreur est survenue.'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'error'   => config('app.debug') ? $e->getMessage() : null,
        ], 500);
    }
}
