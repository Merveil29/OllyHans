<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Requests\Profile\UpdateProfileImageRequest;
use App\Services\Auth\AuthService;
use App\Services\CloudinaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Exception;

class ProfileController extends Controller
{
    protected AuthService $authService;
    protected CloudinaryService $cloudinaryService;

    public function __construct(
        AuthService $authService,
        CloudinaryService $cloudinaryService
    ) {
        $this->authService = $authService;
        $this->cloudinaryService = $cloudinaryService;
    }

    /**
     * Obtenir le profil du client connecté
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        $client = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $client->id_client,
                'nom' => $client->client_nom,
                'prenom' => $client->client_prenom,
                'email' => $client->client_email,
                'login' => $client->client_login,
                'telephone' => $client->client_tel,
                'adresse' => $client->client_adresse,
                'image' => $client->image_client,
                'email_status' => $client->client_email_status,
                'jettons' => $client->client_jettons,
                'jettons_sponsor' => $client->client_jettons_sponsor,
            ]
        ]);
    }

    /**
     * Mettre à jour le profil du client
     *
     * @param UpdateProfileRequest $request
     * @return JsonResponse
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        try {
            $client = $request->user();
            $data = $request->validated();

            $updatedClient = $this->authService->updateClient($client, $data);

            return response()->json([
                'success' => true,
                'message' => 'Profil mis à jour avec succès.',
                'data' => [
                    'id' => $updatedClient->id_client,
                    'nom' => $updatedClient->client_nom,
                    'prenom' => $updatedClient->client_prenom,
                    'email' => $updatedClient->client_email,
                    'login' => $updatedClient->client_login,
                    'telephone' => $updatedClient->client_tel,
                    'adresse' => $updatedClient->client_adresse,
                    'image' => $updatedClient->image_client,
                    'jettons' => $updatedClient->client_jettons,
                    'jettons_sponsor' => $updatedClient->client_jettons_sponsor,
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Erreur lors de la mise à jour du profil: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la mise à jour du profil.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Mettre à jour la photo de profil
     *
     * @param UpdateProfileImageRequest $request
     * @return JsonResponse
     */
    public function updateImage(UpdateProfileImageRequest $request): JsonResponse
    {
        try {
            $client = $request->user();
            $image = $request->file('image');

            // Supprimer l'ancienne image si elle existe sur Cloudinary
            if ($client->image_client && $client->image_client !== 'public/avatar.png') {
                $publicId = $this->cloudinaryService->extractPublicId($client->image_client);
                if ($publicId) {
                    $this->cloudinaryService->deleteImage($publicId);
                }
            }

            // Upload la nouvelle image
            $uploadResult = $this->cloudinaryService->uploadAvatar($image, (string)$client->id_client);

            if (!$uploadResult['success']) {
                throw new Exception('Échec de l\'upload de l\'image');
            }

            // Mettre à jour l'URL de l'image dans la base de données
            $client->update([
                'image_client' => $uploadResult['url']
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
            Log::error('Erreur lors de la mise à jour de la photo: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la mise à jour de la photo.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Supprimer la photo de profil
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteImage(Request $request): JsonResponse
    {
        try {
            $client = $request->user();

            // Supprimer l'image de Cloudinary si elle existe
            if ($client->image_client && $client->image_client !== 'public/avatar.png') {
                $publicId = $this->cloudinaryService->extractPublicId($client->image_client);
                if ($publicId) {
                    $this->cloudinaryService->deleteImage($publicId);
                }
            }

            // Remettre l'avatar par défaut
            $client->update([
                'image_client' => 'public/avatar.png'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Photo de profil supprimée avec succès.',
                'data' => [
                    'image_url' => 'public/avatar.png'
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Erreur lors de la suppression de la photo: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la suppression de la photo.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Changer le mot de passe du client
     *
     * @param Request $request
     * @return JsonResponse
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

            $client = $request->user();

            // Vérifier que le mot de passe actuel est correct
            if (!Hash::check($request->current_password, $client->client_password)) {
                throw ValidationException::withMessages([
                    'current_password' => ['Le mot de passe actuel est incorrect.']
                ]);
            }

            // Mettre à jour le mot de passe
            $client->update([
                'client_password' => Hash::make($request->new_password)
            ]);

            // Révoquer tous les tokens existants pour forcer la reconnexion
            $client->tokens()->delete();

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
            Log::error('Erreur lors du changement de mot de passe: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors du changement de mot de passe.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
