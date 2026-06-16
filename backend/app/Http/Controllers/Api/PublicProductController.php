<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produit;
use App\Models\Vue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Exception;
use OpenApi\Annotations as OA;

class PublicProductController extends Controller
{
    /**
     * Liste tous les produits validés (id_state = 2) avec pagination
     * Affichage public, pas d'authentification requise
     *
     * @param Request $request
     * @return JsonResponse
     * 
     * @OA\Get(
     *     path="/products",
     *     summary="Liste des produits publiés",
     *     description="Récupère la liste paginée des produits validés et visibles publiquement",
     *     operationId="getPublicProducts",
     *     tags={"Produits Publics"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Numéro de page",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Nombre de produits par page",
     *         @OA\Schema(type="integer", default=12)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Recherche par nom ou description",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Critère de tri",
     *         @OA\Schema(type="string", enum={"recent", "price_asc", "price_desc", "name"}, default="recent")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des produits",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Produit")),
     *             @OA\Property(
     *                 property="pagination",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="last_page", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 12); // 12 produits par page
            $page = $request->get('page', 1);
            $search = $request->get('search', '');
            $sort = $request->get('sort', 'recent'); // Type de tri

            $query = Produit::where('id_state', 2) // Seulement publiés
                ->with(['categorie', 'client', 'state'])
                ->withCount('vues');

            // Recherche par nom ou description
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('nom_produits', 'LIKE', '%' . $search . '%')
                      ->orWhere('description_produits', 'LIKE', '%' . $search . '%');
                });
            }

            // Tri
            switch ($sort) {
                case 'price_asc':
                    $query->orderBy('prix_produits', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('prix_produits', 'desc');
                    break;
                case 'name':
                    $query->orderBy('nom_produits', 'asc');
                    break;
                case 'recent':
                default:
                    $query->orderBy('id_produits', 'desc');
                    break;
            }

            // Pagination
            $products = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $products->map(function ($product) {
                    return [
                        'id_produits' => $product->id_produits,
                        'nom_produits' => $product->nom_produits,
                        'prix_produits' => $product->prix_produits,
                        'description_produits' => $product->description_produits,
                        'image_produits' => $product->image_produits,
                        'image_produits1' => $product->image_produits1,
                        'image_produits2' => $product->image_produits2,
                        'dateSaisie' => $product->dateSaisie,
                        'vues_count' => $product->vues_count ?? 0,
                        'categorie' => $product->categorie ? [
                            'id_categorie' => $product->categorie->id_categorie,
                            'nom_categorie' => $product->categorie->nom_categorie,
                        ] : null,
                        'client' => $product->client ? [
                            'nom_client' => $product->client->client_nom ?? null,
                            'prenom_client' => $product->client->client_prenom ?? null,
                        ] : null,
                        'state' => $product->state ? [
                            'id_state' => $product->state->id_state,
                            'title' => $product->state->title,
                        ] : null,
                    ];
                }),
                'pagination' => [
                    'total' => $products->total(),
                    'per_page' => $products->perPage(),
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'from' => $products->firstItem(),
                    'to' => $products->lastItem(),
                ],
            ]);

        } catch (Exception $e) {
            Log::error('Erreur récupération produits publics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des produits.'
            ], 500);
        }
    }

    /**
     * Afficher le détail d'un produit validé
     *
     * @param int $id
     * @return JsonResponse
     * 
     * @OA\Get(
     *     path="/products/{id}",
     *     summary="Détail d'un produit",
     *     description="Récupère les détails complets d'un produit publié",
     *     operationId="getPublicProduct",
     *     tags={"Produits Publics"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du produit",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails du produit",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Produit")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Produit non trouvé ou non publié")
     * )
     */
    public function show(int $id): JsonResponse
    {
        try {
            $product = Produit::where('id_produits', $id)
                ->where('id_state', 2) // Seulement si publié
                ->with(['categorie', 'client', 'state'])
                ->withCount('vues')
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => [
                    'id_produits' => $product->id_produits,
                    'nom_produits' => $product->nom_produits,
                    'prix_produits' => $product->prix_produits,
                    'description_produits' => $product->description_produits,
                    'image_produits' => $product->image_produits,
                    'image_produits1' => $product->image_produits1,
                    'image_produits2' => $product->image_produits2,
                    'dateSaisie' => $product->dateSaisie,
                    'vues_count' => $product->vues_count ?? 0,
                    'categorie' => $product->categorie ? [
                        'id_categorie' => $product->categorie->id_categorie,
                        'nom_categorie' => $product->categorie->nom_categorie,
                    ] : null,
                    'client' => $product->client ? [
                        'nom_client' => $product->client->client_nom ?? null,
                        'prenom_client' => $product->client->client_prenom ?? null,
                        'telephone' => $product->client->client_tel ?? null,
                        'email' => $product->client->client_email ?? null,
                    ] : null,
                    'state' => $product->state ? [
                        'id_state' => $product->state->id_state,
                        'title' => $product->state->title,
                    ] : null,
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Erreur récupération produit public: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Produit non trouvé ou non publié.'
            ], 404);
        }
    }

    /**
     * Enregistrer une vue sur un produit
     * Rate limiting: 1 vue par IP par produit toutes les 30 minutes
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * 
     * @OA\Post(
     *     path="/products/{id}/view",
     *     summary="Enregistrer une vue",
     *     description="Enregistre une vue sur un produit. Limité à 1 vue par IP par produit toutes les 30 minutes.",
     *     operationId="recordProductView",
     *     tags={"Produits Publics"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du produit",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Vue enregistrée",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Vue enregistrée avec succès"),
     *             @OA\Property(property="vues_count", type="integer", example=42)
     *         )
     *     ),
     *     @OA\Response(response=404, description="Produit non trouvé"),
     *     @OA\Response(
     *         response=429,
     *         description="Vue déjà enregistrée (rate limit)",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Vue déjà enregistrée"),
     *             @OA\Property(property="vues_count", type="integer")
     *         )
     *     )
     * )
     */
    public function recordView(Request $request, int $id): JsonResponse
    {
        try {
            // Vérifier que le produit existe et est publié
            $produit = Produit::where('id_produits', $id)
                ->where('id_state', 2)
                ->first();

            if (!$produit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produit non trouvé'
                ], 404);
            }

            $ip = $request->ip();
            
            // Rate limiting: empêcher les vues multiples de la même IP
            $key = "product_view:{$ip}:{$id}";
            
            if (RateLimiter::tooManyAttempts($key, 1)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vue déjà enregistrée',
                    'vues_count' => $produit->vues()->count()
                ], 429);
            }

            // Enregistrer la vue
            Vue::create([
                'IP' => $ip,
                'id_produits' => $id,
            ]);

            // Bloquer les nouvelles vues de cette IP pendant 30 minutes
            RateLimiter::hit($key, 1800); // 30 min = 1800 secondes

            $totalVues = $produit->vues()->count();

            Log::info("Vue enregistrée", [
                'produit_id' => $id,
                'ip' => $ip,
                'total_vues' => $totalVues
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Vue enregistrée avec succès',
                'vues_count' => $totalVues
            ]);

        } catch (Exception $e) {
            Log::error('Erreur enregistrement vue: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement de la vue'
            ], 500);
        }
    }
    /**
     * Obtenir des suggestions de produits basées sur une recherche
     * 
     * @param Request $request
     * @return JsonResponse
     * 
     * @OA\Get(
     *     path="/products/suggestions",
     *     summary="Suggestions de recherche",
     *     description="Retourne jusqu'à 10 produits correspondant à la recherche (minimum 2 caractères)",
     *     operationId="getProductSuggestions",
     *     tags={"Produits Publics"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=true,
     *         description="Terme de recherche (min 2 caractères)",
     *         @OA\Schema(type="string", minLength=2)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des suggestions",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id_produits", type="integer"),
     *                     @OA\Property(property="nom_produits", type="string"),
     *                     @OA\Property(property="prix_produits", type="number"),
     *                     @OA\Property(property="image_produits", type="string")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function suggestions(Request $request): JsonResponse
    {
        try {
            $search = $request->get('search', '');
            
            if (strlen($search) < 2) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            // Rechercher les produits publiés correspondants (max 10 suggestions)
            $products = Produit::where('id_state', 2)
                ->where(function($q) use ($search) {
                    $q->where('nom_produits', 'LIKE', '%' . $search . '%')
                      ->orWhere('description_produits', 'LIKE', '%' . $search . '%');
                })
                ->select('id_produits', 'nom_produits', 'prix_produits', 'image_produits')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $products
            ]);

        } catch (Exception $e) {
            Log::error('Erreur suggestions produits: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'data' => []
            ], 500);
        }
    }}
