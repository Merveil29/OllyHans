<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class VerifyOtpController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Vérifier le code OTP et créer le compte client
     *
     * @param VerifyOtpRequest $request
     * @return JsonResponse
     */
    public function verify(VerifyOtpRequest $request): JsonResponse
    {
        try {
            $email = $request->input('email');
            $otp = $request->input('otp');
            
            // Récupérer les données du cache
            $cacheKey = "registration:{$email}";
            $cachedData = Cache::get($cacheKey);
            
            // Vérifier si les données existent dans le cache
            if (!$cachedData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Code de vérification expiré ou invalide. Veuillez recommencer l\'inscription.',
                ], 400);
            }
            
            // Vérifier le code OTP (conversion en string pour comparaison)
            if ((string)$cachedData['otp'] !== (string)$otp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le code de vérification est incorrect.',
                    'errors' => [
                        'otp' => ['Le code de vérification est incorrect.']
                    ]
                ], 400);
            }
            
            // OTP valide, créer le compte dans la base de données
            $client = $this->authService->registerClient($cachedData['data']);
            
            // Supprimer les données du cache
            Cache::forget($cacheKey);
            
            // Créer un token Sanctum pour connexion automatique
            $token = $client->createToken('auth_token')->plainTextToken;
            
            return response()->json([
                'success' => true,
                'message' => 'Email vérifié avec succès ! Bienvenue sur TOPIDEAL.',
                'data' => [
                    'client' => [
                        'id' => $client->id_client,
                        'nom' => $client->client_nom,
                        'prenom' => $client->client_prenom,
                        'email' => $client->client_email,
                        'login' => $client->client_login,
                        'telephone' => $client->client_tel,
                        'adresse' => $client->client_adresse,
                        'jettons' => $client->client_jettons,
                        'jettons_sponsor' => $client->client_jettons_sponsor,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ], 201);
            
        } catch (Exception $e) {
            Log::error('Erreur lors de la vérification OTP: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la vérification.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
