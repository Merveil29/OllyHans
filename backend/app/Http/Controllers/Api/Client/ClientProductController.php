<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Produit\StoreProduitRequest;
use App\Http\Requests\Produit\UpdateProduitRequest;
use App\Http\Resources\ProduitResource;
use App\Models\Produit;
use App\Services\ProductService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use OpenApi\Annotations as OA;

class ClientProductController extends Controller
{
    use ApiResponse;

    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Liste tous les produits du client connecté (tous états confondus)
     *
     * @param Request $request
     * @return JsonResponse
     * 
     * @OA\Get(
     *     path="/client/products",
     *     summary="Liste des produits du client",
     *     description="Récupère tous les produits du client connecté, quel que soit leur état",
     *     operationId="getClientProducts",
     *     tags={"Produits Client"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des produits",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="produits", type="array", @OA\Items(ref="#/components/schemas/Produit")),
     *                 @OA\Property(property="jetons_restants", type="integer", example=5)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non authentifié")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $client = $request->user();

            $perPage = $request->input('per_page', 20);
            $produits = Produit::where('id_client', $client->id_client)
                ->with(['state', 'categorie'])
                ->orderBy('id_produits', 'desc')
                ->paginate($perPage);

            return $this->success([
                'produits' => ProduitResource::collection($produits),
                'jetons_restants' => $client->client_jettons,
                'pagination' => [
                    'total' => $produits->total(),
                    'per_page' => $produits->perPage(),
                    'current_page' => $produits->currentPage(),
                    'last_page' => $produits->lastPage(),
                ],
            ]);

        } catch (Exception $e) {
            Log::error('Erreur récupération produits client: ' . $e->getMessage());
            return $this->serverError($e);
        }
    }

    /**
     * Afficher le détail d'un produit du client
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * 
     * @OA\Get(
     *     path="/client/products/{id}",
     *     summary="Détail d'un produit client",
     *     description="Récupère les détails d'un produit appartenant au client connecté",
     *     operationId="getClientProduct",
     *     tags={"Produits Client"},
     *     security={{"sanctum": {}}},
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
     *     @OA\Response(response=403, description="Accès refusé"),
     *     @OA\Response(response=404, description="Produit non trouvé")
     * )
     */
    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $produit = Produit::with(['state', 'user', 'categorie'])->findOrFail($id);

            // Vérifier que le produit appartient au client
            $client = $request->user();
            if ($produit->id_client !== $client->id_client) {
                return $this->forbidden('Accès refusé.');
            }

            return $this->success(new ProduitResource($produit));

        } catch (Exception $e) {
            Log::error('Erreur récupération produit: ' . $e->getMessage());
            return $this->notFound('Produit non trouvé.');
        }
    }

    /**
     * Créer un nouveau produit
     *
     * @param Request $request
     * @return JsonResponse
     * 
     * @OA\Post(
     *     path="/client/products",
     *     summary="Créer un produit",
     *     description="Crée un nouveau produit. Nécessite au moins 1 jeton. Le produit sera en attente de validation.",
     *     operationId="createClientProduct",
     *     tags={"Produits Client"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"nom_produits", "prix_produits", "description_produits", "id_sous_categorie", "image_produits"},
     *                 @OA\Property(property="nom_produits", type="string", maxLength=255),
     *                 @OA\Property(property="prix_produits", type="number", minimum=0),
     *                 @OA\Property(property="description_produits", type="string"),
     *                 @OA\Property(property="id_sous_categorie", type="integer"),
     *                 @OA\Property(property="image_produits", type="string", format="binary"),
     *                 @OA\Property(property="image_produits1", type="string", format="binary"),
     *                 @OA\Property(property="image_produits2", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Produit créé",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Produit créé avec succès. En attente de validation."),
     *             @OA\Property(property="data", ref="#/components/schemas/Produit")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Jetons insuffisants"),
     *     @OA\Response(response=422, description="Erreur de validation")
     * )
     */
    public function store(StoreProduitRequest $request): JsonResponse
    {
        try {
            $client = $request->user();

            // Vérifier que le client a des jetons
            if (!$this->productService->canClientCreateProduct($client)) {
                return $this->error('Jetons insuffisants. Vous devez avoir au moins 1 jeton pour créer un produit.', [
                    'jetons_disponibles' => $client->client_jettons,
                ], 403);
            }

            $data = $request->validated();
            $imageMain = $request->file('image_produits');
            $image1 = $request->file('image_produits1');
            $image2 = $request->file('image_produits2');

            $produit = $this->productService->createProductForClient($client, $data, $imageMain, $image1, $image2);
            $produit->load(['state', 'categorie']);

            return $this->success(
                array_merge((new ProduitResource($produit))->toArray($request), [
                    'jetons_restants' => $client->fresh()->client_jettons,
                ]),
                'Produit créé avec succès. En attente de validation par un administrateur.',
                201
            );

        } catch (Exception $e) {
            Log::error('Erreur création produit: ' . $e->getMessage());
            return $this->serverError($e);
        }
    }

    /**
     * Mettre à jour un produit
     * Seulement si pas encore publié (id_state !== 2)
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * 
     * @OA\Put(
     *     path="/client/products/{id}",
     *     summary="Modifier un produit",
     *     description="Met à jour un produit. Impossible si déjà publié.",
     *     operationId="updateClientProduct",
     *     tags={"Produits Client"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du produit",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="nom_produits", type="string", maxLength=255),
     *                 @OA\Property(property="prix_produits", type="number", minimum=0),
     *                 @OA\Property(property="description_produits", type="string"),
     *                 @OA\Property(property="id_sous_categorie", type="integer"),
     *                 @OA\Property(property="image_produits", type="string", format="binary"),
     *                 @OA\Property(property="image_produits1", type="string", format="binary"),
     *                 @OA\Property(property="image_produits2", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Produit mis à jour",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", ref="#/components/schemas/Produit")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Produit déjà publié ou accès refusé"),
     *     @OA\Response(response=404, description="Produit non trouvé")
     * )
     */
    public function update(UpdateProduitRequest $request, int $id): JsonResponse
    {
        try {
            $produit = Produit::findOrFail($id);

            // Vérifier que le produit appartient au client
            $client = $request->user();
            if ($produit->id_client !== $client->id_client) {
                return $this->forbidden('Accès refusé.');
            }

            // Vérifier si le produit peut être modifié
            if ($produit->id_state === 2) {
                return $this->error('Ce produit a déjà été publié et ne peut plus être modifié.', [], 403);
            }

            $data = $request->validated();
            $imageMain = $request->file('image_produits');
            $image1 = $request->file('image_produits1');
            $image2 = $request->file('image_produits2');

            $produit = $this->productService->updateProduct($produit, $data, $imageMain, $image1, $image2);
            $produit->load(['state', 'categorie']);

            return $this->success(new ProduitResource($produit), 'Produit mis à jour avec succès.');

        } catch (Exception $e) {
            Log::error('Erreur mise à jour produit: ' . $e->getMessage());
            return $this->serverError($e);
        }
    }

    /**
     * Supprimer un produit
     * Peut être supprimé même si validé
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * 
     * @OA\Delete(
     *     path="/client/products/{id}",
     *     summary="Supprimer un produit",
     *     description="Supprime un produit du client connecté",
     *     operationId="deleteClientProduct",
     *     tags={"Produits Client"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du produit",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Produit supprimé",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Produit supprimé avec succès.")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Accès refusé"),
     *     @OA\Response(response=404, description="Produit non trouvé")
     * )
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $produit = Produit::findOrFail($id);

            // Vérifier que le produit appartient au client
            $client = $request->user();
            if ($produit->id_client !== $client->id_client) {
                return $this->forbidden('Accès refusé.');
            }

            $this->productService->deleteProduct($produit);

            return $this->success(null, 'Produit supprimé avec succès.');

        } catch (Exception $e) {
            Log::error('Erreur suppression produit: ' . $e->getMessage());
            return $this->serverError($e);
        }
    }
}
