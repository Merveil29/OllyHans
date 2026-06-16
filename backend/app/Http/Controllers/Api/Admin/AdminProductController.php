<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produit;
use App\Services\ProductService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;
use OpenApi\Annotations as OA;

class AdminProductController extends Controller
{
    use ApiResponse;
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Liste tous les produits (tous clients, tous états)
     * Peut filtrer par état via query param
     *
     * @param Request $request
     * @return JsonResponse
     * 
     * @OA\Get(
     *     path="/admin/products",
     *     summary="Liste des produits (admin)",
     *     description="Récupère tous les produits avec filtres optionnels par état, client, catégorie",
     *     operationId="adminGetProducts",
     *     tags={"Admin Produits"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="state", in="query", description="Filtrer par id_state (1=attente, 2=publié, 3=refusé, 4=premium, 5=désactivé)", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="client", in="query", description="Filtrer par ID client", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="categorie", in="query", description="Filtrer par ID catégorie", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="sous_categorie", in="query", description="Filtrer par ID sous-catégorie", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="search", in="query", description="Recherche par nom ou description", @OA\Schema(type="string")),
     *     @OA\Parameter(name="per_page", in="query", description="Nombre par page", @OA\Schema(type="integer", default=20)),
     *     @OA\Response(
     *         response=200,
     *         description="Liste paginée des produits",
     *         @OA\JsonContent(ref="#/components/schemas/PaginatedResponse")
     *     ),
     *     @OA\Response(response=401, description="Non authentifié"),
     *     @OA\Response(response=403, description="Accès refusé")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Produit::with(['client', 'user', 'state', 'categorie'])
                ->orderBy('id_produits', 'desc');

            // Filtrer par état si demandé (1=En Instance, 2=Publier, 3=Refuser, 4=Prenium, 5=Désactiver)
            if ($request->has('state') && $request->state !== '') {
                $query->where('id_state', $request->state);
            }

            // Filtrer par client si demandé
            if ($request->has('client') && $request->client !== '') {
                $query->where('id_client', $request->client);
            }

            // Filtrer par catégorie si demandé
            if ($request->has('categorie') && $request->categorie !== '') {
                $query->where('id_categorie', $request->categorie);
            }

            // Recherche par nom ou description de produit si demandé
            if ($request->has('search') && $request->search !== '') {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nom_produits', 'LIKE', '%' . $search . '%')
                      ->orWhere('description_produits', 'LIKE', '%' . $search . '%');
                });
            }

            $perPage  = $request->input('per_page', 20);
            $produits = $query->paginate($perPage);

            return $this->paginated($produits, function ($produit) {
                return [
                    'id_produits'          => $produit->id_produits,
                    'nom_produits'         => $produit->nom_produits,
                    'prix_produits'        => $produit->prix_produits,
                    'description_produits' => $produit->description_produits,
                    'image_produits'       => $produit->image_produits,
                    'image_produits1'      => $produit->image_produits1,
                    'image_produits2'      => $produit->image_produits2,
                    'dateSaisie'           => $produit->dateSaisie,
                    'categorie'            => $produit->categorie ? [
                        'id_categorie'  => $produit->categorie->id_categorie,
                        'nom_categorie' => $produit->categorie->nom_categorie,
                    ] : null,
                    'client' => $produit->client ? [
                        'id_client' => $produit->client->id_client,
                        'nom'       => $produit->client->client_nom . ' ' . $produit->client->client_prenom,
                        'email'     => $produit->client->client_email,
                    ] : null,
                    'validated_by' => $produit->user ? [
                        'id_user' => $produit->user->id_user,
                        'nom'     => $produit->user->user_nom . ' ' . $produit->user->user_prenom,
                    ] : null,
                    'state' => $produit->state ? [
                        'id_state' => $produit->state->id_state,
                        'title'    => $produit->state->title,
                    ] : null,
                ];
            });

        } catch (Exception $e) {
            Log::error('Erreur récupération produits admin: ' . $e->getMessage());
            return $this->serverError($e, 'Erreur lors de la récupération des produits.');
        }
    }

    /**
     * Afficher le détail d'un produit
     *
     * @param int $id
     * @return JsonResponse
     * 
     * @OA\Get(
     *     path="/admin/products/{id}",
     *     summary="Détail d'un produit (admin)",
     *     description="Récupère les détails complets d'un produit",
     *     operationId="adminGetProduct",
     *     tags={"Admin Produits"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Détails du produit", @OA\JsonContent(@OA\Property(property="success", type="boolean"), @OA\Property(property="data", ref="#/components/schemas/Produit"))),
     *     @OA\Response(response=404, description="Produit non trouvé")
     * )
     */
    public function show(int $id): JsonResponse
    {
        try {
            $produit = Produit::with(['client', 'user', 'state', 'categorie'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id_produits' => $produit->id_produits,
                    'nom_produits' => $produit->nom_produits,
                    'prix_produits' => $produit->prix_produits,
                    'description_produits' => $produit->description_produits,
                    'image_produits' => $produit->image_produits,
                    'image_produits1' => $produit->image_produits1,
                    'image_produits2' => $produit->image_produits2,
                    'dateSaisie' => $produit->dateSaisie,
                    'categorie' => $produit->categorie ? [
                        'id_categorie' => $produit->categorie->id_categorie,
                        'nom_categorie' => $produit->categorie->nom_categorie,
                    ] : null,
                    'client' => $produit->client ? [
                        'id_client' => $produit->client->id_client,
                        'nom' => $produit->client->client_nom . ' ' . $produit->client->client_prenom,
                        'email' => $produit->client->client_email,
                        'telephone' => $produit->client->client_tel,
                    ] : null,
                    'validated_by' => $produit->user ? [
                        'id_user' => $produit->user->id_user,
                        'nom' => $produit->user->user_nom . ' ' . $produit->user->user_prenom,
                    ] : null,
                    'state' => $produit->state ? [
                        'id_state' => $produit->state->id_state,
                        'title' => $produit->state->title,
                    ] : null,
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Erreur récupération produit admin: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Produit non trouvé.'
            ], 404);
        }
    }

    /**
     * Créer un produit (admin)
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @OA\Post(
     *     path="/admin/products",
     *     summary="Créer un produit (admin)",
     *     description="Crée un nouveau produit sans passer par le flux client",
     *     operationId="adminStoreProduct",
     *     tags={"Admin Produits"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"nom_produits", "prix_produits", "description_produits", "id_sous_categorie"},
     *                 @OA\Property(property="nom_produits", type="string"),
     *                 @OA\Property(property="prix_produits", type="number"),
     *                 @OA\Property(property="description_produits", type="string"),
     *                 @OA\Property(property="id_sous_categorie", type="integer"),
     *                 @OA\Property(property="id_state", type="integer", default=1),
     *                 @OA\Property(property="image_produits", type="string", format="binary"),
     *                 @OA\Property(property="image_produits1", type="string", format="binary"),
     *                 @OA\Property(property="image_produits2", type="string", format="binary"),
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Produit créé"),
     *     @OA\Response(response=422, description="Erreur de validation")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'nom_produits'         => 'required|string|max:255',
                'prix_produits'        => 'required|numeric|min:0',
                'description_produits' => 'required|string',
                'id_categorie'         => 'required|integer|exists:categorie,id_categorie',
                'id_state'             => 'integer|in:1,2,3,4,5',
                'image_produits'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'image_produits1'      => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'image_produits2'      => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $data = $request->only([
                'nom_produits', 'prix_produits', 'description_produits',
                'id_categorie', 'id_state',
            ]);
            $data['id_state'] = $data['id_state'] ?? 1;

            $admin = $request->user();

            $imageMain = $request->file('image_produits');
            $image1    = $request->file('image_produits1');
            $image2    = $request->file('image_produits2');

            $produit = $this->productService->createProductForAdmin(
                $admin, $data, $imageMain, $image1, $image2
            );

            return response()->json([
                'success' => true,
                'message' => 'Produit créé avec succès.',
                'data'    => ['id_produits' => $produit->id_produits],
            ], 201);

        } catch (Exception $e) {
            Log::error('Erreur création produit admin: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mettre à jour un produit (admin)
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     *
     * @OA\Post(
     *     path="/admin/products/{id}",
     *     summary="Mettre à jour un produit (admin)",
     *     description="Met à jour un produit existant",
     *     operationId="adminUpdateProduct",
     *     tags={"Admin Produits"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="nom_produits", type="string"),
     *                 @OA\Property(property="prix_produits", type="number"),
     *                 @OA\Property(property="description_produits", type="string"),
     *                 @OA\Property(property="id_sous_categorie", type="integer"),
     *                 @OA\Property(property="id_state", type="integer"),
     *                 @OA\Property(property="image_produits", type="string", format="binary"),
     *                 @OA\Property(property="image_produits1", type="string", format="binary"),
     *                 @OA\Property(property="image_produits2", type="string", format="binary"),
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Produit mis à jour"),
     *     @OA\Response(response=404, description="Produit non trouvé"),
     *     @OA\Response(response=422, description="Erreur de validation")
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $produit = Produit::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'nom_produits'         => 'string|max:255',
                'prix_produits'        => 'numeric|min:0',
                'description_produits' => 'string',
                'id_categorie'    => 'integer|exists:categorie,id_categorie',
                'id_state'             => 'integer|in:1,2,3,4,5',
                'image_produits'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'image_produits1'      => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'image_produits2'      => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $data = $request->only([
                'nom_produits', 'prix_produits', 'description_produits',
                'id_categorie', 'id_state',
            ]);
            $data = array_filter($data, fn($v) => $v !== null && $v !== '');

            $imageMain = $request->file('image_produits');
            $image1    = $request->file('image_produits1');
            $image2    = $request->file('image_produits2');

            $produit = $this->productService->updateProduct(
                $produit, $data, $imageMain, $image1, $image2
            );

            return response()->json([
                'success' => true,
                'message' => 'Produit mis à jour avec succès.',
                'data'    => ['id_produits' => $produit->id_produits],
            ]);

        } catch (Exception $e) {
            Log::error('Erreur mise à jour produit admin: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Approuver/Publier un produit (passer en état "Publier", id_state = 2)
     * Renommé de validate() → approve() pour éviter le conflit avec ValidatesRequests de Laravel
     * 
     * @OA\Post(
     *     path="/admin/products/{id}/approve",
     *     summary="Approuver un produit",
     *     description="Valide un produit en attente et l'envoie vers l'état 'Publié'",
     *     operationId="adminApproveProduct",
     *     tags={"Admin Produits"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(@OA\JsonContent(@OA\Property(property="comment", type="string", description="Commentaire optionnel pour le client"))),
     *     @OA\Response(response=200, description="Produit approuvé"),
     *     @OA\Response(response=400, description="Produit déjà validé ou rejeté")
     * )
     */
    public function approve(Request $request, int $id): JsonResponse
    {
        try {
            $produit = Produit::with(['client', 'state'])->findOrFail($id);

            // Vérifier que le produit est en attente
            if ($produit->id_state === 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce produit est déjà validé.'
                ], 400);
            }

            if ($produit->id_state === 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce produit a été rejeté. Vous ne pouvez pas le valider.'
                ], 400);
            }

            // Récupérer le commentaire optionnel
            $comment = $request->input('comment', null);

            $admin = $request->user();
            $produit = $this->productService->validateProduct($produit, $admin, $comment);

            $message = 'Produit validé avec succès.';
            if (!empty($comment)) {
                $message .= ' Un email a été envoyé au client.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'id_produits' => $produit->id_produits,
                    'nom_produits' => $produit->nom_produits,
                    'state' => $produit->state ? [
                        'id_state' => $produit->state->id_state,
                        'title' => $produit->state->title,
                    ] : null,
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Erreur validation produit: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refuser un produit (le passer en état "Refuser")
     * id_state = 3
     * Recrédite 1 jeton au client
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * 
     * @OA\Post(
     *     path="/admin/products/{id}/reject",
     *     summary="Rejeter un produit",
     *     description="Rejette un produit en attente. Le jeton est recrédité au client.",
     *     operationId="adminRejectProduct",
     *     tags={"Admin Produits"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(required={"reason"}, @OA\Property(property="reason", type="string", minLength=10, description="Raison du rejet"))),
     *     @OA\Response(response=200, description="Produit rejeté, jeton recrédité"),
     *     @OA\Response(response=400, description="Produit déjà validé ou rejeté"),
     *     @OA\Response(response=422, description="Raison manquante ou trop courte")
     * )
     */
    public function reject(Request $request, int $id): JsonResponse
    {
        try {
            $produit = Produit::with(['client', 'state'])->findOrFail($id);

            // Vérifier que le produit est en attente
            if ($produit->id_state === 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce produit est déjà validé. Vous ne pouvez pas le rejeter.'
                ], 400);
            }

            if ($produit->id_state === 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce produit est déjà rejeté.'
                ], 400);
            }

            // Valider que la raison est fournie et non vide
            $validator = Validator::make($request->all(), [
                'reason' => 'required|string|min:10',
            ], [
                'reason.required' => 'La raison du rejet est obligatoire.',
                'reason.min' => 'La raison doit contenir au moins 10 caractères.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }

            $admin = $request->user();
            $reason = $request->input('reason');
            $produit = $this->productService->rejectProduct($produit, $admin, $reason);

            return response()->json([
                'success' => true,
                'message' => 'Produit rejeté avec succès. Le jeton a été recrédité au client et un email a été envoyé.',
                'data' => [
                    'id_produits' => $produit->id_produits,
                    'nom_produits' => $produit->nom_produits,
                    'state' => $produit->state ? [
                        'id_state' => $produit->state->id_state,
                        'title' => $produit->state->title,
                    ] : null,
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Erreur rejet produit: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un produit
     *
     * @param int $id
     * @return JsonResponse
     * 
     * @OA\Delete(
     *     path="/admin/products/{id}",
     *     summary="Supprimer un produit",
     *     description="Supprime un produit et ses images associées",
     *     operationId="adminDeleteProduct",
     *     tags={"Admin Produits"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Produit supprimé"),
     *     @OA\Response(response=404, description="Produit non trouvé")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $produit = Produit::findOrFail($id);

            $this->productService->deleteProduct($produit);

            return response()->json([
                'success' => true,
                'message' => 'Produit supprimé avec succès.'
            ]);

        } catch (Exception $e) {
            Log::error('Erreur suppression produit admin: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du produit.'
            ], 500);
        }
    }

    /**
     * Statistiques des produits — 1 seule requête SQL agrégée
     * 
     * @OA\Get(
     *     path="/admin/products/stats",
     *     summary="Statistiques des produits",
     *     description="Récupère les statistiques agrégées par état",
     *     operationId="adminGetProductStats",
     *     tags={"Admin Produits"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Statistiques des produits",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer"),
     *                 @OA\Property(property="en_attente", type="integer"),
     *                 @OA\Property(property="publies", type="integer"),
     *                 @OA\Property(property="refuses", type="integer"),
     *                 @OA\Property(property="premium", type="integer"),
     *                 @OA\Property(property="desactives", type="integer")
     *             )
     *         )
     *     )
     * )
     */
    public function stats(): JsonResponse
    {
        try {
            $row = Produit::selectRaw("
                COUNT(*)             AS total,
                SUM(id_state = 1)    AS en_attente,
                SUM(id_state = 2)    AS publies,
                SUM(id_state = 3)    AS refuses,
                SUM(id_state = 4)    AS premium,
                SUM(id_state = 5)    AS desactives
            ")->first();

            return $this->success([
                'total'      => (int) $row->total,
                'en_attente' => (int) $row->en_attente,
                'publies'    => (int) $row->publies,
                'refuses'    => (int) $row->refuses,
                'premium'    => (int) $row->premium,
                'desactives' => (int) $row->desactives,
            ]);

        } catch (Exception $e) {
            Log::error('Erreur récupération stats produits: ' . $e->getMessage());
            return $this->serverError($e, 'Erreur lors de la récupération des statistiques.');
        }
    }
}
