<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Services\CloudinaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Exception;
use OpenApi\Annotations as OA;

class AdminCategoryController extends Controller
{
    protected CloudinaryService $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    /**
     * Récupérer toutes les catégories avec le nombre de sous-catégories et produits
     * 
     * @OA\Get(
     *     path="/admin/categories",
     *     summary="Liste des catégories",
     *     description="Récupère toutes les catégories avec le nombre de sous-catégories et produits associés",
     *     operationId="adminGetCategories",
     *     tags={"Admin Catégories"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des catégories",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id_categorie", type="integer"),
     *                 @OA\Property(property="nom_categorie", type="string"),
     *                 @OA\Property(property="image_categorie", type="string"),
     *                 @OA\Property(property="sous_categories_count", type="integer"),
     *                 @OA\Property(property="produits_count", type="integer"),
     *                 @OA\Property(property="can_delete", type="boolean"),
     *                 @OA\Property(property="can_modify", type="boolean")
     *             ))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non authentifié"),
     *     @OA\Response(response=403, description="Accès refusé")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 50);
            $categories = Categorie::withCount(['sousCategories', 'produits'])
                ->orderBy('nom_categorie')
                ->paginate($perPage)
                ->through(function ($categorie) {
                    return [
                        'id' => $categorie->id_categorie,
                        'id_categorie' => $categorie->id_categorie,
                        'nom' => $categorie->nom_categorie,
                        'nom_categorie' => $categorie->nom_categorie,
                        'image' => $categorie->image_categorie,
                        'image_categorie' => $categorie->image_categorie,
                        'sous_categories_count' => $categorie->sous_categories_count ?? 0,
                        'produits_count' => $categorie->produits_count ?? 0,
                        'can_delete' => $categorie->produits_count == 0,
                        'can_modify' => $categorie->produits_count == 0,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $categories->items(),
                'pagination' => [
                    'total' => $categories->total(),
                    'per_page' => $categories->perPage(),
                    'current_page' => $categories->currentPage(),
                    'last_page' => $categories->lastPage(),
                ],
            ]);
        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération des catégories: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des catégories.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Récupérer une catégorie spécifique avec ses sous-catégories
     * 
     * @OA\Get(
     *     path="/admin/categories/{id}",
     *     summary="Détail d'une catégorie",
     *     description="Récupère une catégorie avec ses sous-catégories et le nombre de produits",
     *     operationId="adminGetCategory",
     *     tags={"Admin Catégories"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID de la catégorie", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Détails de la catégorie", @OA\JsonContent(@OA\Property(property="success", type="boolean"), @OA\Property(property="data", ref="#/components/schemas/Categorie"))),
     *     @OA\Response(response=404, description="Catégorie non trouvée")
     * )
     */
    public function show($id): JsonResponse
    {
        try {
            $categorie = Categorie::with(['sousCategories' => function ($query) {
                $query->withCount('produits');
            }])
                ->withCount('produits')
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $categorie->id_categorie,
                    'id_categorie' => $categorie->id_categorie,
                    'nom' => $categorie->nom_categorie,
                    'nom_categorie' => $categorie->nom_categorie,
                    'image' => $categorie->image_categorie,
                    'image_categorie' => $categorie->image_categorie,
                    'produits_count' => $categorie->produits_count,
                    'can_delete' => $categorie->produits_count == 0,
                    'can_modify' => $categorie->produits_count == 0,
                    'sous_categories' => $categorie->sousCategories->map(function ($sc) {
                        return [
                            'id_sous_categorie' => $sc->id_sous_categorie,
                            'libelle_sous_categorie' => $sc->libelle_sous_categorie,
                            'produits_count' => $sc->produits_count ?? 0,
                            'can_delete' => ($sc->produits_count ?? 0) == 0,
                            'can_modify' => ($sc->produits_count ?? 0) == 0,
                        ];
                    })
                ]
            ]);
        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération de la catégorie: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Catégorie non trouvée.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 404);
        }
    }

    /**
     * Créer une nouvelle catégorie
     * 
     * @OA\Post(
     *     path="/admin/categories",
     *     summary="Créer une catégorie",
     *     description="Crée une nouvelle catégorie avec une image",
     *     operationId="adminCreateCategory",
     *     tags={"Admin Catégories"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"nom_categorie", "image"},
     *                 @OA\Property(property="nom_categorie", type="string", maxLength=255, description="Nom de la catégorie"),
     *                 @OA\Property(property="image", type="string", format="binary", description="Image de la catégorie")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Catégorie créée avec succès"),
     *     @OA\Response(response=422, description="Erreur de validation")
     * )
     */
    public function create(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'nom_categorie' => 'required|string|max:255|unique:categorie,nom_categorie',
                'image' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
            ]);

            // Upload de l'image sur Cloudinary
            $image = $request->file('image');
            $uploadResult = $this->cloudinaryService->uploadCategoryImage($image);

            if (!$uploadResult['success']) {
                throw new Exception('Échec de l\'upload de l\'image');
            }

            // Créer la catégorie
            $categorie = Categorie::create([
                'nom_categorie' => $request->nom_categorie,
                'image_categorie' => $uploadResult['url'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Catégorie créée avec succès.',
                'data' => [
                    'id_categorie' => $categorie->id_categorie,
                    'nom_categorie' => $categorie->nom_categorie,
                    'image_categorie' => $categorie->image_categorie,
                ]
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation.',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            Log::error('Erreur lors de la création de la catégorie: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la catégorie.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Mettre à jour une catégorie
     * 
     * @OA\Put(
     *     path="/admin/categories/{id}",
     *     summary="Mettre à jour une catégorie",
     *     description="Met à jour le nom et/ou l'image d'une catégorie. Impossible si la catégorie contient des produits.",
     *     operationId="adminUpdateCategory",
     *     tags={"Admin Catégories"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID de la catégorie", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"nom_categorie"},
     *                 @OA\Property(property="nom_categorie", type="string", maxLength=255, description="Nom de la catégorie"),
     *                 @OA\Property(property="image", type="string", format="binary", description="Nouvelle image")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Catégorie mise à jour"),
     *     @OA\Response(response=403, description="Catégorie contient des produits"),
     *     @OA\Response(response=404, description="Catégorie non trouvée"),
     *     @OA\Response(response=422, description="Erreur de validation")
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $categorie = Categorie::withCount('produits')->findOrFail($id);

            // Vérifier si la catégorie contient des produits
            if ($categorie->produits_count > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de modifier une catégorie contenant des produits.',
                ], 403);
            }

            $request->validate([
                'nom_categorie' => 'required|string|max:255|unique:categorie,nom_categorie,' . $id . ',id_categorie',
                'image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
            ]);

            $updateData = [
                'nom_categorie' => $request->nom_categorie,
            ];

            // Si une nouvelle image est fournie
            if ($request->hasFile('image')) {
                // Supprimer l'ancienne image de Cloudinary
                if ($categorie->image_categorie) {
                    $publicId = $this->cloudinaryService->extractPublicId($categorie->image_categorie);
                    if ($publicId) {
                        $this->cloudinaryService->deleteImage($publicId);
                    }
                }

                // Upload de la nouvelle image
                $image = $request->file('image');
                $uploadResult = $this->cloudinaryService->uploadCategoryImage($image);

                if (!$uploadResult['success']) {
                    throw new Exception('Échec de l\'upload de l\'image');
                }

                $updateData['image_categorie'] = $uploadResult['url'];
            }

            $categorie->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Catégorie mise à jour avec succès.',
                'data' => [
                    'id_categorie' => $categorie->id_categorie,
                    'nom_categorie' => $categorie->nom_categorie,
                    'image_categorie' => $categorie->image_categorie,
                ]
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation.',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            Log::error('Erreur lors de la mise à jour de la catégorie: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la catégorie.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Supprimer une catégorie
     * 
     * @OA\Delete(
     *     path="/admin/categories/{id}",
     *     summary="Supprimer une catégorie",
     *     description="Supprime une catégorie et ses sous-catégories. Impossible si la catégorie contient des produits.",
     *     operationId="adminDeleteCategory",
     *     tags={"Admin Catégories"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID de la catégorie", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Catégorie supprimée avec succès"),
     *     @OA\Response(response=403, description="Catégorie contient des produits"),
     *     @OA\Response(response=404, description="Catégorie non trouvée")
     * )
     */
    public function delete($id): JsonResponse
    {
        try {
            $categorie = Categorie::withCount('produits')->findOrFail($id);

            // Vérifier si la catégorie contient des produits
            if ($categorie->produits_count > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer une catégorie contenant des produits.',
                ], 403);
            }

            // Supprimer les sous-catégories associées
            $categorie->sousCategories()->delete();

            // Supprimer l'image de Cloudinary
            if ($categorie->image_categorie) {
                $publicId = $this->cloudinaryService->extractPublicId($categorie->image_categorie);
                if ($publicId) {
                    $this->cloudinaryService->deleteImage($publicId);
                }
            }

            // Supprimer la catégorie
            $categorie->delete();

            return response()->json([
                'success' => true,
                'message' => 'Catégorie supprimée avec succès.',
            ]);
        } catch (Exception $e) {
            Log::error('Erreur lors de la suppression de la catégorie: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la catégorie.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Récupérer les produits validés d'une catégorie (endpoint public)
     * 
     * @OA\Get(
     *     path="/admin/categories/{id}/products",
     *     summary="Produits d'une catégorie",
     *     description="Récupère les produits validés (publiés) d'une catégorie avec pagination",
     *     operationId="adminGetCategoryProducts",
     *     tags={"Admin Catégories"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID de la catégorie", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", description="Nombre par page", @OA\Schema(type="integer", default=12)),
     *     @OA\Parameter(name="page", in="query", description="Numéro de page", @OA\Schema(type="integer", default=1)),
     *     @OA\Parameter(name="search", in="query", description="Rechercher par nom ou description", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Liste paginée des produits"),
     *     @OA\Response(response=404, description="Catégorie non trouvée")
     * )
     */
    public function getProducts($id, Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 12);
            $page = $request->input('page', 1);
            $search = $request->input('search', '');

            // Vérifier que la catégorie existe
            $categorie = Categorie::findOrFail($id);

            // Récupérer les produits validés (publiés) de cette catégorie avec pagination
            $query = $categorie->produits()
                ->where('id_state', 2) // 2 = Publier
                ->with(['categorie', 'client'])
                ->withCount('vues');

            // Recherche par nom ou description
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('nom_produits', 'LIKE', '%' . $search . '%')
                      ->orWhere('description_produits', 'LIKE', '%' . $search . '%');
                });
            }

            $products = $query->orderBy('id_produits', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

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
                    ];
                }),
                'pagination' => [
                    'total' => $products->total(),
                    'per_page' => $products->perPage(),
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'from' => $products->firstItem(),
                    'to' => $products->lastItem(),
                ]
            ]);
        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération des produits de la catégorie: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des produits.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 404);
        }
    }
}
