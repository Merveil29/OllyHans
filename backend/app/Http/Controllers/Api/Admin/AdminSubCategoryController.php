<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SousCategorie;
use App\Models\Categorie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Exception;
use OpenApi\Annotations as OA;

class AdminSubCategoryController extends Controller
{
    /**
     * Récupérer toutes les sous-catégories avec leurs catégories
     * 
     * @OA\Get(
     *     path="/admin/sub-categories/all",
     *     summary="Toutes les sous-catégories",
     *     description="Récupère toutes les sous-catégories groupées par catégorie",
     *     operationId="adminGetAllSubCategories",
     *     tags={"Admin Sous-Catégories"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des sous-catégories par catégorie",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id_categorie", type="integer"),
     *                 @OA\Property(property="nom_categorie", type="string"),
     *                 @OA\Property(property="sous_categories", type="array", @OA\Items(type="object"))
     *             ))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non authentifié")
     * )
     */
    public function all(): JsonResponse
    {
        try {
            $categories = Categorie::with(['sousCategories' => function ($query) {
                $query->orderBy('libelle_sous_categorie');
            }])
            ->whereHas('sousCategories') // Seulement les catégories qui ont des sous-catégories
            ->orderBy('nom_categorie')
            ->get()
            ->map(function ($categorie) {
                return [
                    'id_categorie' => $categorie->id_categorie,
                    'nom_categorie' => $categorie->nom_categorie,
                    'sous_categories' => $categorie->sousCategories->map(function ($sc) {
                        return [
                            'id_sous_categorie' => $sc->id_sous_categorie,
                            'libelle_sous_categorie' => $sc->libelle_sous_categorie,
                        ];
                    })
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération de toutes les sous-catégories: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des sous-catégories.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Récupérer toutes les sous-catégories d'une catégorie
     * 
     * @OA\Get(
     *     path="/admin/categories/{categoryId}/sub-categories",
     *     summary="Sous-catégories d'une catégorie",
     *     description="Récupère toutes les sous-catégories d'une catégorie spécifique",
     *     operationId="adminGetSubCategoriesByCategory",
     *     tags={"Admin Sous-Catégories"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="categoryId", in="path", required=true, description="ID de la catégorie", @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des sous-catégories",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id_sous_categorie", type="integer"),
     *                 @OA\Property(property="libelle_sous_categorie", type="string"),
     *                 @OA\Property(property="produits_count", type="integer"),
     *                 @OA\Property(property="can_delete", type="boolean"),
     *                 @OA\Property(property="can_modify", type="boolean")
     *             ))
     *         )
     *     )
     * )
     */
    public function index($categoryId): JsonResponse
    {
        try {
            $sousCategories = SousCategorie::where('id_categorie', $categoryId)
                ->orderBy('libelle_sous_categorie')
                ->get()
                ->map(function ($sousCategorie) {
                    return [
                        'id_sous_categorie' => $sousCategorie->id_sous_categorie,
                        'libelle_sous_categorie' => $sousCategorie->libelle_sous_categorie,
                        'id_categorie' => $sousCategorie->id_categorie,
                        'produits_count' => 0,
                        'can_delete' => true,
                        'can_modify' => true,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $sousCategories
            ]);
        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération des sous-catégories: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des sous-catégories.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Récupérer une sous-catégorie spécifique avec ses produits
     * 
     * @OA\Get(
     *     path="/admin/sub-categories/{id}",
     *     summary="Détail d'une sous-catégorie",
     *     description="Récupère une sous-catégorie avec ses produits associés",
     *     operationId="adminGetSubCategory",
     *     tags={"Admin Sous-Catégories"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID de la sous-catégorie", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Détails de la sous-catégorie", @OA\JsonContent(@OA\Property(property="success", type="boolean"), @OA\Property(property="data", ref="#/components/schemas/SousCategorie"))),
     *     @OA\Response(response=404, description="Sous-catégorie non trouvée")
     * )
     */
    public function show($id): JsonResponse
    {
        try {
            $sousCategorie = SousCategorie::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id_sous_categorie' => $sousCategorie->id_sous_categorie,
                    'libelle_sous_categorie' => $sousCategorie->libelle_sous_categorie,
                    'id_categorie' => $sousCategorie->id_categorie,
                    'produits_count' => 0,
                    'can_delete' => true,
                    'can_modify' => true,
                    'produits' => [],
                ]
            ]);
        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération de la sous-catégorie: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sous-catégorie non trouvée.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 404);
        }
    }

    /**
     * Créer une nouvelle sous-catégorie
     * 
     * @OA\Post(
     *     path="/admin/sub-categories",
     *     summary="Créer une sous-catégorie",
     *     description="Crée une nouvelle sous-catégorie dans une catégorie existante",
     *     operationId="adminCreateSubCategory",
     *     tags={"Admin Sous-Catégories"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"libelle_sous_categorie", "id_categorie"},
     *             @OA\Property(property="libelle_sous_categorie", type="string", maxLength=120, description="Libellé de la sous-catégorie"),
     *             @OA\Property(property="id_categorie", type="integer", description="ID de la catégorie parente")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Sous-catégorie créée avec succès"),
     *     @OA\Response(response=422, description="Erreur de validation ou sous-catégorie existante")
     * )
     */
    public function create(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'libelle_sous_categorie' => 'required|string|max:120',
                'id_categorie' => 'required|exists:categorie,id_categorie',
            ]);

            // Vérifier l'unicité de la sous-catégorie dans la catégorie
            $exists = SousCategorie::where('id_categorie', $request->id_categorie)
                ->where('libelle_sous_categorie', $request->libelle_sous_categorie)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette sous-catégorie existe déjà dans cette catégorie.',
                    'errors' => [
                        'libelle_sous_categorie' => ['Cette sous-catégorie existe déjà dans cette catégorie.']
                    ]
                ], 422);
            }

            $sousCategorie = SousCategorie::create([
                'libelle_sous_categorie' => $request->libelle_sous_categorie,
                'id_categorie' => $request->id_categorie,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sous-catégorie créée avec succès.',
                'data' => [
                    'id_sous_categorie' => $sousCategorie->id_sous_categorie,
                    'libelle_sous_categorie' => $sousCategorie->libelle_sous_categorie,
                    'id_categorie' => $sousCategorie->id_categorie,
                    'produits_count' => 0,
                    'can_delete' => true,
                    'can_modify' => true,
                ]
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation.',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            Log::error('Erreur lors de la création de la sous-catégorie: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la sous-catégorie.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Mettre à jour une sous-catégorie
     * 
     * @OA\Put(
     *     path="/admin/sub-categories/{id}",
     *     summary="Mettre à jour une sous-catégorie",
     *     description="Met à jour le libellé d'une sous-catégorie. Impossible si elle contient des produits.",
     *     operationId="adminUpdateSubCategory",
     *     tags={"Admin Sous-Catégories"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID de la sous-catégorie", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"libelle_sous_categorie"},
     *             @OA\Property(property="libelle_sous_categorie", type="string", maxLength=120, description="Nouveau libellé")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Sous-catégorie mise à jour"),
     *     @OA\Response(response=403, description="Sous-catégorie contient des produits"),
     *     @OA\Response(response=404, description="Sous-catégorie non trouvée"),
     *     @OA\Response(response=422, description="Erreur de validation")
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $sousCategorie = SousCategorie::findOrFail($id);

            $request->validate([
                'libelle_sous_categorie' => 'required|string|max:120',
            ]);

            // Vérifier l'unicité de la sous-catégorie dans la catégorie
            $exists = SousCategorie::where('id_categorie', $sousCategorie->id_categorie)
                ->where('libelle_sous_categorie', $request->libelle_sous_categorie)
                ->where('id_sous_categorie', '!=', $id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette sous-catégorie existe déjà dans cette catégorie.',
                    'errors' => [
                        'libelle_sous_categorie' => ['Cette sous-catégorie existe déjà dans cette catégorie.']
                    ]
                ], 422);
            }

            $sousCategorie->update([
                'libelle_sous_categorie' => $request->libelle_sous_categorie,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sous-catégorie mise à jour avec succès.',
                'data' => [
                    'id_sous_categorie' => $sousCategorie->id_sous_categorie,
                    'libelle_sous_categorie' => $sousCategorie->libelle_sous_categorie,
                    'id_categorie' => $sousCategorie->id_categorie,
                ]
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation.',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            Log::error('Erreur lors de la mise à jour de la sous-catégorie: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la sous-catégorie.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Supprimer une sous-catégorie
     * 
     * @OA\Delete(
     *     path="/admin/sub-categories/{id}",
     *     summary="Supprimer une sous-catégorie",
     *     description="Supprime une sous-catégorie. Impossible si elle contient des produits.",
     *     operationId="adminDeleteSubCategory",
     *     tags={"Admin Sous-Catégories"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID de la sous-catégorie", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Sous-catégorie supprimée avec succès"),
     *     @OA\Response(response=403, description="Sous-catégorie contient des produits"),
     *     @OA\Response(response=404, description="Sous-catégorie non trouvée")
     * )
     */
    public function delete($id): JsonResponse
    {
        try {
            $sousCategorie = SousCategorie::findOrFail($id);

            $sousCategorie->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sous-catégorie supprimée avec succès.',
            ]);
        } catch (Exception $e) {
            Log::error('Erreur lors de la suppression de la sous-catégorie: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la sous-catégorie.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
