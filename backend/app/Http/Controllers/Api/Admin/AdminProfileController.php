<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\CloudinaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Exception;
use OpenApi\Annotations as OA;

class AdminProfileController extends Controller
{
    protected CloudinaryService $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    /**
     * Obtenir le profil de l'administrateur connecté
     *
     * @param Request $request
     * @return JsonResponse
     * 
     * @OA\Get(
     *     path="/admin/profile",
     *     summary="Profil de l'admin connecté",
     *     description="Récupère les informations du profil de l'administrateur authentifié",
     *     operationId="adminGetProfile",
     *     tags={"Admin Profil"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Informations du profil",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="user_nom", type="string"),
     *                 @OA\Property(property="user_prenom", type="string"),
     *                 @OA\Property(property="user_email", type="string"),
     *                 @OA\Property(property="user_login", type="string"),
     *                 @OA\Property(property="user_tel", type="string"),
     *                 @OA\Property(property="user_image", type="string", nullable=true),
     *                 @OA\Property(property="user_email_status", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non authentifié")
     * )
     */
    public function show(Request $request): JsonResponse
    {
        $admin = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $admin->id_user,
                'user_nom' => $admin->user_nom,
                'user_prenom' => $admin->user_prenom,
                'user_email' => $admin->user_email,
                'user_login' => $admin->user_login,
                'user_tel' => $admin->user_tel,
                'user_image' => $admin->image_user,
                'user_email_status' => $admin->user_email_status,
            ]
        ]);
    }

    /**
     * Mettre à jour le profil de l'administrateur
     *
     * @param Request $request
     * @return JsonResponse
     * 
     * @OA\Put(
     *     path="/admin/profile",
     *     summary="Mettre à jour le profil",
     *     description="Met à jour les informations de profil de l'administrateur connecté",
     *     operationId="adminUpdateProfile",
     *     tags={"Admin Profil"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_nom", "user_prenom"},
     *             @OA\Property(property="user_nom", type="string", maxLength=150, description="Nom"),
     *             @OA\Property(property="user_prenom", type="string", maxLength=30, description="Prénom"),
     *             @OA\Property(property="user_tel", type="string", maxLength=255, nullable=true, description="Téléphone")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Profil mis à jour avec succès"),
     *     @OA\Response(response=422, description="Erreur de validation")
     * )
     */
    public function update(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'user_nom' => 'required|string|max:150',
                'user_prenom' => 'required|string|max:30',
                'user_tel' => 'nullable|string|max:255',
            ]);

            $admin = $request->user();

            $admin->update([
                'user_nom' => $request->user_nom,
                'user_prenom' => $request->user_prenom,
                'user_tel' => $request->user_tel,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Profil mis à jour avec succès.',
                'data' => [
                    'id' => $admin->id_user,
                    'user_nom' => $admin->user_nom,
                    'user_prenom' => $admin->user_prenom,
                    'user_email' => $admin->user_email,
                    'user_login' => $admin->user_login,
                    'user_tel' => $admin->user_tel,
                    'user_image' => $admin->image_user,
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Erreur lors de la mise à jour du profil admin: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la mise à jour du profil.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Mettre à jour la photo de profil de l'administrateur
     *
     * @param Request $request
     * @return JsonResponse
     * 
     * @OA\Post(
     *     path="/admin/profile/image",
     *     summary="Mettre à jour la photo de profil",
     *     description="Upload et met à jour la photo de profil de l'administrateur",
     *     operationId="adminUpdateProfileImage",
     *     tags={"Admin Profil"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"image"},
     *                 @OA\Property(property="image", type="string", format="binary", description="Image de profil (max 5MB)")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Photo mise à jour", @OA\JsonContent(@OA\Property(property="success", type="boolean"), @OA\Property(property="data", type="object", @OA\Property(property="image_url", type="string"), @OA\Property(property="image_width", type="integer"), @OA\Property(property="image_height", type="integer")))),
     *     @OA\Response(response=422, description="Erreur de validation")
     * )
     */
    public function updateImage(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:5120', // 5MB max
            ]);

            $admin = $request->user();
            $image = $request->file('image');

            // Supprimer l'ancienne image si elle existe sur Cloudinary
            if ($admin->image_user && $admin->image_user !== 'public/avatar.png') {
                $publicId = $this->cloudinaryService->extractPublicId($admin->image_user);
                if ($publicId) {
                    $this->cloudinaryService->deleteImage($publicId);
                }
            }

            // Upload la nouvelle image
            $uploadResult = $this->cloudinaryService->uploadAvatar($image, 'admin_' . (string)$admin->id_user);

            if (!$uploadResult['success']) {
                throw new Exception('Échec de l\'upload de l\'image');
            }

            // Mettre à jour l'URL de l'image dans la base de données
            $admin->update([
                'image_user' => $uploadResult['url']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Photo de profil mise à jour avec succès.',
                'data' => [
                    'image_url' => $uploadResult['url'],
                    'image_width' => $uploadResult['width'],
                    'image_height' => $uploadResult['height'],
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Erreur lors de la mise à jour de la photo admin: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la mise à jour de la photo.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Supprimer la photo de profil de l'administrateur
     *
     * @param Request $request
     * @return JsonResponse
     * 
     * @OA\Delete(
     *     path="/admin/profile/image",
     *     summary="Supprimer la photo de profil",
     *     description="Supprime la photo de profil et remet l'avatar par défaut",
     *     operationId="adminDeleteProfileImage",
     *     tags={"Admin Profil"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(response=200, description="Photo supprimée avec succès")
     * )
     */
    public function deleteImage(Request $request): JsonResponse
    {
        try {
            $admin = $request->user();

            // Supprimer l'image de Cloudinary si elle existe
            if ($admin->image_user && $admin->image_user !== 'public/avatar.png') {
                $publicId = $this->cloudinaryService->extractPublicId($admin->image_user);
                if ($publicId) {
                    $this->cloudinaryService->deleteImage($publicId);
                }
            }

            // Remettre l'avatar par défaut
            $admin->update([
                'image_user' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Photo de profil supprimée avec succès.',
            ]);

        } catch (Exception $e) {
            Log::error('Erreur lors de la suppression de la photo admin: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la suppression de la photo.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Changer le mot de passe de l'administrateur
     *
     * @param Request $request
     * @return JsonResponse
     * 
     * @OA\Put(
     *     path="/admin/profile/password",
     *     summary="Changer le mot de passe",
     *     description="Change le mot de passe de l'administrateur. Révoque tous les tokens existants.",
     *     operationId="adminUpdatePassword",
     *     tags={"Admin Profil"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"current_password", "new_password", "new_password_confirmation"},
     *             @OA\Property(property="current_password", type="string", description="Mot de passe actuel"),
     *             @OA\Property(property="new_password", type="string", minLength=8, description="Nouveau mot de passe"),
     *             @OA\Property(property="new_password_confirmation", type="string", description="Confirmation du nouveau mot de passe")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Mot de passe modifié. Reconnexion requise."),
     *     @OA\Response(response=422, description="Mot de passe actuel incorrect ou validation échouée")
     * )
     */
    public function updatePassword(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
            ], [
                'current_password.required' => 'Le mot de passe actuel est requis.',
                'new_password.required' => 'Le nouveau mot de passe est requis.',
                'new_password.min' => 'Le nouveau mot de passe doit contenir au moins 8 caractères.',
                'new_password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            ]);

            $admin = $request->user();

            // Vérifier que le mot de passe actuel est correct
            if (!Hash::check($request->current_password, $admin->user_password)) {
                throw ValidationException::withMessages([
                    'current_password' => ['Le mot de passe actuel est incorrect.']
                ]);
            }

            // Mettre à jour le mot de passe
            $admin->update([
                'user_password' => Hash::make($request->new_password)
            ]);

            // Révoquer tous les tokens existants pour forcer la reconnexion
            $admin->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Mot de passe modifié avec succès. Veuillez vous reconnecter.',
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation.',
                'errors' => $e->errors()
            ], 422);

        } catch (Exception $e) {
            Log::error('Erreur lors du changement de mot de passe admin: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors du changement de mot de passe.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
