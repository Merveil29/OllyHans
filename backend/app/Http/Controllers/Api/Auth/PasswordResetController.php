<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Mail\ResetPasswordMail;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;

class PasswordResetController extends Controller
{
    /**
     * Demander un code de réinitialisation de mot de passe
     *
     * @param ForgotPasswordRequest $request
     * @return JsonResponse
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            $email = $request->input('email');
            
            // Récupérer le client
            $client = Client::where('client_email', $email)->first();
            
            if (!$client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun compte trouvé avec cet email.',
                ], 404);
            }
            
            // Générer un code OTP à 6 chiffres
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Stocker l'OTP dans le cache pour 10 minutes
            Cache::put("password_reset:{$email}", [
                'otp' => $otp,
                'client_id' => $client->id_client,
                'created_at' => now()
            ], now()->addMinutes(10));
            
            // Envoyer l'email avec le code OTP
            $clientName = $client->client_prenom . ' ' . $client->client_nom;
            Mail::to($email)->send(new ResetPasswordMail($otp, $clientName));
            
            Log::info("Code de réinitialisation envoyé à {$email}: {$otp}");
            
            return response()->json([
                'success' => true,
                'message' => 'Un code de vérification a été envoyé à votre adresse email.',
                'data' => [
                    'email' => $email,
                    'expires_in' => '10 minutes',
                    'otp' => config('app.debug') ? $otp : null, // Seulement en debug
                ]
            ], 200);

        } catch (Exception $e) {
            Log::error('Erreur lors de la demande de réinitialisation: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la demande.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Réinitialiser le mot de passe avec le code OTP
     *
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $email = $request->input('email');
            $otp = $request->input('otp');
            $newPassword = $request->input('password');
            
            // Récupérer les données du cache
            $cacheKey = "password_reset:{$email}";
            $cachedData = Cache::get($cacheKey);
            
            // Vérifier si le code existe dans le cache
            if (!$cachedData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Code de vérification expiré ou invalide. Veuillez redemander un code.',
                ], 400);
            }
            
            // Vérifier le code OTP
            if ($cachedData['otp'] !== $otp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le code de vérification est incorrect.',
                    'errors' => [
                        'otp' => ['Le code de vérification est incorrect.']
                    ]
                ], 400);
            }
            
            // Récupérer le client
            $client = Client::find($cachedData['client_id']);
            
            if (!$client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Compte introuvable.',
                ], 404);
            }
            
            // Mettre à jour le mot de passe
            $client->update([
                'client_password' => Hash::make($newPassword),
                'client_otp' => $otp, // Sauvegarder le dernier OTP utilisé
            ]);
            
            // Supprimer le code du cache
            Cache::forget($cacheKey);
            
            // Révoquer tous les tokens existants pour forcer la reconnexion
            $client->tokens()->delete();
            
            Log::info("Mot de passe réinitialisé avec succès pour {$email}");
            
            return response()->json([
                'success' => true,
                'message' => 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.',
            ], 200);

        } catch (Exception $e) {
            Log::error('Erreur lors de la réinitialisation du mot de passe: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la réinitialisation.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Vérifier si un code OTP est valide
     *
     * @param string $email
     * @param string $otp
     * @return JsonResponse
     */
    public function verifyResetOtp(string $email, string $otp): JsonResponse
    {
        $cacheKey = "password_reset:{$email}";
        $cachedData = Cache::get($cacheKey);
        
        if (!$cachedData) {
            return response()->json([
                'valid' => false,
                'message' => 'Code expiré ou invalide.'
            ], 400);
        }
        
        $isValid = $cachedData['otp'] === $otp;
        
        return response()->json([
            'valid' => $isValid,
            'message' => $isValid ? 'Code valide.' : 'Code invalide.'
        ], $isValid ? 200 : 400);
    }
}
