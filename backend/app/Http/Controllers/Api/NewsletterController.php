<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

class NewsletterController extends Controller
{
    /**
     * S'abonner à la newsletter.
     *
     * @OA\Post(
     *     path="/newsletter/subscribe",
     *     summary="S'abonner à la newsletter",
     *     description="Inscrit un email à la newsletter TOPIDEALSPACE",
     *     operationId="newsletterSubscribe",
     *     tags={"Newsletter"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="nom", type="string", example="Jean Dupont"),
     *             @OA\Property(property="notify_products", type="boolean", example=true),
     *             @OA\Property(property="notify_blog", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Abonnement réussi"),
     *     @OA\Response(response=200, description="Abonnement réactivé"),
     *     @OA\Response(response=422, description="Erreur de validation")
     * )
     */
    public function subscribe(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email'           => 'required|email|max:255',
            'nom'             => 'nullable|string|max:255',
            'notify_products' => 'nullable|boolean',
            'notify_blog'     => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $email = strtolower(trim($request->email));

        // Vérifier si l'email existe déjà
        $existing = NewsletterSubscriber::where('email', $email)->first();

        if ($existing) {
            if ($existing->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cet email est déjà abonné à la newsletter.',
                ], 409);
            }

            // Réactiver un abonné inactif
            $existing->update([
                'is_active'       => true,
                'nom'             => $request->nom ?? $existing->nom,
                'notify_products' => $request->boolean('notify_products', true),
                'notify_blog'     => $request->boolean('notify_blog', true),
                'unsubscribed_at' => null,
                'subscribed_at'   => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Votre abonnement a été réactivé avec succès !',
            ]);
        }

        // Nouvel abonné
        NewsletterSubscriber::create([
            'email'           => $email,
            'nom'             => strip_tags($request->nom ?? ''),
            'notify_products' => $request->boolean('notify_products', true),
            'notify_blog'     => $request->boolean('notify_blog', true),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bienvenue ! Vous êtes inscrit à la newsletter TOPIDEALSPACE.',
        ], 201);
    }

    /**
     * Se désabonner de la newsletter via un token unique.
     *
     * @OA\Get(
     *     path="/newsletter/unsubscribe/{token}",
     *     summary="Se désabonner de la newsletter",
     *     description="Désactive l'abonnement à la newsletter via un lien unique",
     *     operationId="newsletterUnsubscribe",
     *     tags={"Newsletter"},
     *     @OA\Parameter(name="token", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Désabonnement réussi"),
     *     @OA\Response(response=404, description="Token invalide")
     * )
     */
    public function unsubscribe(string $token): JsonResponse
    {
        $subscriber = NewsletterSubscriber::where('unsubscribe_token', $token)->first();

        if (!$subscriber) {
            return response()->json([
                'success' => false,
                'message' => 'Lien de désinscription invalide ou expiré.',
            ], 404);
        }

        $subscriber->update([
            'is_active'       => false,
            'unsubscribed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Vous avez été désabonné de la newsletter avec succès.',
        ]);
    }

    /**
     * Statistiques newsletter (admin).
     *
     * @OA\Get(
     *     path="/admin/newsletter/stats",
     *     summary="Statistiques de la newsletter",
     *     operationId="newsletterStats",
     *     tags={"Admin Newsletter"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(response=200, description="Statistiques")
     * )
     */
    public function stats(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => [
                'total'              => NewsletterSubscriber::count(),
                'active'             => NewsletterSubscriber::active()->count(),
                'inactive'           => NewsletterSubscriber::where('is_active', false)->count(),
                'notify_products'    => NewsletterSubscriber::wantsProducts()->count(),
                'notify_blog'        => NewsletterSubscriber::wantsBlog()->count(),
            ],
        ]);
    }

    /**
     * Liste des abonnés (admin).
     *
     * @OA\Get(
     *     path="/admin/newsletter/subscribers",
     *     summary="Liste des abonnés newsletter",
     *     operationId="newsletterSubscribers",
     *     tags={"Admin Newsletter"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=20)),
     *     @OA\Parameter(name="status", in="query", description="active|inactive|all", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Liste paginée des abonnés")
     * )
     */
    public function subscribers(Request $request): JsonResponse
    {
        $query = NewsletterSubscriber::query()->orderBy('created_at', 'desc');

        $status = $request->input('status', 'all');
        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        $perPage = (int) $request->input('per_page', 20);
        $subscribers = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $subscribers,
        ]);
    }
}
