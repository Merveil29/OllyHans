<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\AuthService;
use App\Models\Client;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use OpenApi\Annotations as OA;

class LoginController extends Controller
{
    use ApiResponse;
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Connexion commune pour clients et admins
     * 
     * - Clients: Connexion directe avec token
     * - Admins: Génération OTP + envoi email + session temporaire
     *
     * @param LoginRequest $request
     * @return JsonResponse
     * 
     * @OA\Post(
     *     path="/login",
     *     summary="Connexion utilisateur",
     *     description="Connecte un client (token direct) ou un admin (OTP envoyé par email)",
     *     operationId="login",
     *     tags={"Authentification"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"identifier", "password"},
     *             @OA\Property(property="identifier", type="string", example="jean.dupont@gmail.com", description="Email ou login"),
     *             @OA\Property(property="password", type="string", format="password", example="MonMotDePasse123!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Connexion réussie (client) ou OTP envoyé (admin)",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="user_type", type="string", enum={"client", "admin"}),
     *                 @OA\Property(property="user", ref="#/components/schemas/Client"),
     *                 @OA\Property(property="token", type="string", description="Bearer token (client uniquement)"),
     *                 @OA\Property(property="requires_otp", type="boolean", description="True si admin (OTP requis)")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Identifiants incorrects"),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     ),
     *     @OA\Response(response=429, description="Trop de requêtes")
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $identifier = $request->input('identifier');
            $password = $request->input('password');

            // Vérifier si c'est un email ou un login
            $isEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL);
            
            // Chercher dans la table clients d'abord
            if ($isEmail) {
                $client = Client::where('client_email', $identifier)
                    ->where('client_email_status', 'vérifier')
                    ->first();
            } else {
                $client = Client::where('client_login', $identifier)
                    ->where('client_email_status', 'vérifier')
                    ->first();
            }

            // Vérification mot de passe client
            // Support anciens mots de passe en clair (importés) → migration auto vers bcrypt
            $clientPasswordValid = false;
            if ($client) {
                $bcryptCheckPassed = false;
                try {
                    $bcryptCheckPassed = Hash::check($password, $client->client_password);
                } catch (\RuntimeException $e) {
                    // Hash non-bcrypt (MD5, SHA1, ou autre algo ancien) → on ignore et on tente le plaintext
                    Log::warning('Hash non-bcrypt détecté pour client #' . $client->id_client . ' : ' . $e->getMessage());
                }

                if ($bcryptCheckPassed) {
                    $clientPasswordValid = true;
                } elseif ($client->client_password === $password) {
                    // Ancien compte plaintext → on migre vers bcrypt silencieusement
                    $client->client_password = Hash::make($password);
                    $client->save();
                    $clientPasswordValid = true;
                    Log::info('Migration mot de passe plaintext → bcrypt pour client #' . $client->id_client);
                }
            }

            if ($clientPasswordValid) {
                $token = $client->createToken('auth-token')->plainTextToken;

                return $this->success([
                    'user_type' => 'client',
                    'user' => [
                        'id'              => $client->id_client,
                        'nom'             => $client->client_nom,
                        'prenom'          => $client->client_prenom,
                        'email'           => $client->client_email,
                        'login'           => $client->client_login,
                        'telephone'       => $client->client_tel,
                        'adresse'         => $client->client_adresse,
                        'image'           => $client->image_client,
                        'jettons'         => $client->client_jettons,
                        'jettons_sponsor' => $client->client_jettons_sponsor,
                    ],
                    'token' => $token,
                ], 'Connexion réussie.');
            }

            // Chercher dans la table users (admins)
            if ($isEmail) {
                $admin = User::where('user_email', $identifier)->first();
            } else {
                $admin = User::where('user_login', $identifier)->first();
            }

            // Vérification mot de passe admin + migration plaintext si nécessaire
            $adminPasswordValid = false;
            if ($admin) {
                $bcryptCheckPassedAdmin = false;
                try {
                    $bcryptCheckPassedAdmin = Hash::check($password, $admin->user_password);
                } catch (\RuntimeException $e) {
                    // Hash non-bcrypt → on ignore et on tente le plaintext
                    Log::warning('Hash non-bcrypt détecté pour admin #' . $admin->id_user . ' : ' . $e->getMessage());
                }

                if ($bcryptCheckPassedAdmin) {
                    $adminPasswordValid = true;
                } elseif ($admin->user_password === $password) {
                    $admin->user_password = Hash::make($password);
                    $admin->save();
                    $adminPasswordValid = true;
                    Log::info('Migration mot de passe plaintext → bcrypt pour admin #' . $admin->id_user);
                }
            }

            if ($adminPasswordValid) {
                // CONNEXION ADMIN - direct token, no OTP
                $token = $admin->createToken('admin-auth-token')->plainTextToken;

                if ($admin->user_email_status === 'non vérifier') {
                    $admin->user_email_status = 'vérifier';
                    $admin->save();
                }

                return $this->success([
                    'user_type' => 'admin',
                    'user' => [
                        'id'           => $admin->id_user,
                        'nom'          => $admin->user_nom,
                        'prenom'       => $admin->user_prenom,
                        'email'        => $admin->user_email,
                        'login'        => $admin->user_login,
                        'telephone'    => $admin->user_tel,
                        'image'        => $admin->image_user,
                        'email_status' => $admin->user_email_status,
                    ],
                    'token' => $token,
                ], 'Connexion réussie.');
            }

            return $this->error(
                'Identifiants incorrects.',
                ['identifier' => ['L\'email/login ou le mot de passe est incorrect.']],
                401
            );

        } catch (\Exception $e) {
            Log::error('Erreur lors de la connexion: ' . $e->getMessage());
            return $this->serverError($e, 'Une erreur est survenue lors de la connexion.');
        }
    }

    /**
     * Déconnexion d'un client
     *
     * @param Request $request
     * @return JsonResponse
     * 
     * @OA\Post(
     *     path="/logout",
     *     summary="Déconnexion",
     *     description="Déconnecte l'utilisateur en supprimant le token actuel",
     *     operationId="logout",
     *     tags={"Authentification"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Déconnexion réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Déconnexion réussie.")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non authentifié")
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            // Supprimer le token actuel
            $request->user()->currentAccessToken()->delete();

            return $this->success(null, 'Déconnexion réussie.');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la déconnexion: ' . $e->getMessage());
            return $this->serverError($e, 'Une erreur est survenue lors de la déconnexion.');
        }
    }
}
