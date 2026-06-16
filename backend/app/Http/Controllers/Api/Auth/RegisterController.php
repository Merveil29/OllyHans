<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\Auth\AuthService;
use App\Mail\OtpVerificationMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;
use OpenApi\Annotations as OA;

class RegisterController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Enregistrer un nouveau client (étape 1: envoi OTP)
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     * 
     * @OA\Post(
     *     path="/register",
     *     summary="Inscription d'un nouveau client",
     *     description="Crée un compte client et envoie un code OTP par email pour vérification",
     *     operationId="register",
     *     tags={"Authentification"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nom", "prenom", "email", "login", "telephone", "adresse", "password", "password_confirmation"},
     *             @OA\Property(property="nom", type="string", example="DUPONT", description="Nom de famille"),
     *             @OA\Property(property="prenom", type="string", example="Jean", description="Prénom"),
     *             @OA\Property(property="email", type="string", format="email", example="jean.dupont@gmail.com"),
     *             @OA\Property(property="login", type="string", example="jeandupont", description="Identifiant unique (4-50 caractères, alphanumérique)"),
     *             @OA\Property(property="telephone", type="string", example="+22997123456"),
     *             @OA\Property(property="adresse", type="string", example="Cotonou, Bénin"),
     *             @OA\Property(property="password", type="string", format="password", example="MonMotDePasse123!", description="Min 8 chars, majuscule, minuscule, chiffre, symbole"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="MonMotDePasse123!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP envoyé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Un code de vérification a été envoyé à votre adresse email."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="email", type="string", example="jean.dupont@gmail.com"),
     *                 @OA\Property(property="expires_in", type="string", example="10 minutes")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     ),
     *     @OA\Response(response=429, description="Trop de requêtes"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $email = $data['email'];
            
            // Générer un code OTP à 6 chiffres
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Ajouter l'OTP aux données pour sauvegarde en base
            $data['client_otp'] = $otp;
            
            // Stocker les données et l'OTP dans le cache pour 10 minutes
            Cache::put("registration:{$email}", [
                'data' => $data,
                'otp' => $otp,
                'created_at' => now()
            ], now()->addMinutes(10));
            
            // Envoyer l'email avec le code OTP
            $clientName = $data['prenom'] . ' ' . $data['nom'];
            Mail::to($email)->send(new OtpVerificationMail($otp, $clientName));
            
            Log::info("OTP envoyé à {$email}: {$otp}");
            
            // En développement, on retourne le code dans la réponse
            // En production, supprimer 'otp' de la réponse
            return response()->json([
                'success' => true,
                'message' => 'Un code de vérification a été envoyé à votre adresse email. Veuillez vérifier votre boîte de réception.',
                'data' => [
                    'email' => $email,
                    'expires_in' => '10 minutes',
                    'otp' => config('app.debug') ? $otp : null, // Seulement en debug
                ]
            ], 200);

        } catch (Exception $e) {
            Log::error('Erreur lors de l\'inscription: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'inscription.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Vérifier la disponibilité d'un email
     *
     * @param string $email
     * @return JsonResponse
     * 
     * @OA\Get(
     *     path="/check-email/{email}",
     *     summary="Vérifier la disponibilité d'un email",
     *     description="Vérifie si l'adresse email est déjà utilisée par un autre compte",
     *     operationId="checkEmail",
     *     tags={"Authentification"},
     *     @OA\Parameter(
     *         name="email",
     *         in="path",
     *         required=true,
     *         description="Adresse email à vérifier",
     *         @OA\Schema(type="string", format="email", example="jean.dupont@gmail.com")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Résultat de la vérification",
     *         @OA\JsonContent(
     *             @OA\Property(property="available", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Cet email est disponible.")
     *         )
     *     )
     * )
     */
    public function checkEmail(string $email): JsonResponse
    {
        $exists = $this->authService->emailExists($email);

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Cet email est déjà utilisé.' : 'Cet email est disponible.'
        ]);
    }

    /**
     * Vérifier la disponibilité d'un login
     *
     * @param string $login
     * @return JsonResponse
     * 
     * @OA\Get(
     *     path="/check-login/{login}",
     *     summary="Vérifier la disponibilité d'un login",
     *     description="Vérifie si le login/pseudo est déjà utilisé par un autre compte",
     *     operationId="checkLogin",
     *     tags={"Authentification"},
     *     @OA\Parameter(
     *         name="login",
     *         in="path",
     *         required=true,
     *         description="Login à vérifier",
     *         @OA\Schema(type="string", example="jeandupont")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Résultat de la vérification",
     *         @OA\JsonContent(
     *             @OA\Property(property="available", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Ce login est disponible.")
     *         )
     *     )
     * )
     */
    public function checkLogin(string $login): JsonResponse
    {
        $exists = $this->authService->loginExists($login);

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Ce login est déjà utilisé.' : 'Ce login est disponible.'
        ]);
    }
}
