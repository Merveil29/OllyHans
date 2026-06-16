<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\AdminCredentialsMail;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use OpenApi\Annotations as OA;

class AdminUserController extends Controller
{
    use ApiResponse;
    /**
     * Créer un nouvel administrateur
     * 
     * Seul un administrateur authentifié peut créer un autre admin
     *
     * @param Request $request
     * @return JsonResponse
     * 
     * @OA\Post(
     *     path="/admin/users",
     *     summary="Créer un administrateur",
     *     description="Crée un nouveau compte administrateur. Un mot de passe est généré et envoyé par email.",
     *     operationId="adminCreateUser",
     *     tags={"Admin Utilisateurs"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_nom", "user_prenom", "user_email", "user_login", "user_tel"},
     *             @OA\Property(property="user_nom", type="string", maxLength=150, description="Nom de l'administrateur"),
     *             @OA\Property(property="user_prenom", type="string", maxLength=30, description="Prénom"),
     *             @OA\Property(property="user_email", type="string", format="email", maxLength=250, description="Email unique"),
     *             @OA\Property(property="user_login", type="string", maxLength=250, description="Login unique"),
     *             @OA\Property(property="user_tel", type="string", maxLength=255, description="Téléphone")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Administrateur créé avec succès", @OA\JsonContent(@OA\Property(property="message", type="string"), @OA\Property(property="data", type="object", @OA\Property(property="user", type="object"), @OA\Property(property="email_sent", type="boolean")))),
     *     @OA\Response(response=422, description="Erreur de validation")
     * )
     */
    public function create(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_nom' => 'required|string|max:150',
                'user_prenom' => 'required|string|max:30',
                'user_email' => 'required|email|max:250|unique:users,user_email|unique:clients,client_email',
                'user_login' => 'required|string|max:250|unique:users,user_login|unique:clients,client_login',
                'user_tel' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return $this->error('Erreurs de validation', 422, $validator->errors()->toArray());
            }

            // Générer un mot de passe aléatoire sécurisé
            $plainPassword = Str::random(16);
            
            // Générer un code d'activation
            $activationCode = Str::random(32);
            
            // Générer un OTP initial
            $initialOtp = random_int(100000, 999999);

            // Créer l'utilisateur admin
            $user = User::create([
                'user_nom' => $request->user_nom,
                'user_prenom' => $request->user_prenom,
                'user_email' => $request->user_email,
                'user_login' => $request->user_login,
                'user_tel' => $request->user_tel,
                'user_password' => Hash::make($plainPassword),
                'user_activation_code' => $activationCode,
                'user_email_status' => 'non vérifier',
                'user_otp' => $initialOtp,
                'etape' => 1,
                'image_user' => null,
            ]);

            // Envoyer l'email avec les identifiants
            try {
                Mail::to($user->user_email)->send(new AdminCredentialsMail(
                    $user,
                    $plainPassword,
                    $initialOtp
                ));

                $emailSent = true;
            } catch (\Exception $e) {
                Log::error('Erreur lors de l\'envoi de l\'email admin: ' . $e->getMessage());
                $emailSent = false;
            }

            return $this->success([
                'user' => [
                    'id' => $user->id_user,
                    'nom' => $user->user_nom,
                    'prenom' => $user->user_prenom,
                    'email' => $user->user_email,
                    'login' => $user->user_login,
                    'telephone' => $user->user_tel,
                ],
                'email_sent' => $emailSent,
            ], 'Administrateur créé avec succès.', 201);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de l\'admin: ' . $e->getMessage());
            
            return $this->error('Une erreur est survenue lors de la création de l\'administrateur.', 500);
        }
    }

    /**
     * Liste tous les administrateurs
     *
     * @return JsonResponse
     * 
     * @OA\Get(
     *     path="/admin/users",
     *     summary="Liste des administrateurs",
     *     description="Récupère la liste de tous les comptes administrateurs",
     *     operationId="adminGetUsers",
     *     tags={"Admin Utilisateurs"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des administrateurs",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id_user", type="integer"),
     *                 @OA\Property(property="user_nom", type="string"),
     *                 @OA\Property(property="user_prenom", type="string"),
     *                 @OA\Property(property="user_email", type="string"),
     *                 @OA\Property(property="user_login", type="string"),
     *                 @OA\Property(property="user_tel", type="string"),
     *                 @OA\Property(property="user_email_status", type="string"),
     *                 @OA\Property(property="image_user", type="string", nullable=true)
     *             ))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non authentifié")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 20);
            $users = User::select([
                'id_user',
                'user_nom',
                'user_prenom',
                'user_email',
                'user_login',
                'user_tel',
                'user_email_status',
                'image_user',
                'etape'
            ])->paginate($perPage);

            return $this->success([
                'users' => $users->items(),
                'pagination' => [
                    'total' => $users->total(),
                    'per_page' => $users->perPage(),
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                ],
            ], 'Liste des administrateurs récupérée avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des admins: ' . $e->getMessage());
            
            return $this->error('Une erreur est survenue.', 500);
        }
    }

    /**
     * Voir les détails d'un administrateur
     *
     * @param int $id
     * @return JsonResponse
     * 
     * @OA\Get(
     *     path="/admin/users/{id}",
     *     summary="Détail d'un administrateur",
     *     description="Récupère les informations détaillées d'un administrateur",
     *     operationId="adminGetUser",
     *     tags={"Admin Utilisateurs"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID de l'administrateur", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Détails de l'administrateur", @OA\JsonContent(@OA\Property(property="message", type="string"), @OA\Property(property="data", ref="#/components/schemas/User"))),
     *     @OA\Response(response=404, description="Administrateur non trouvé")
     * )
     */
    public function show(int $id): JsonResponse
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return $this->error('Administrateur non trouvé.', 404);
            }

            return $this->success([
                'id' => $user->id_user,
                'nom' => $user->user_nom,
                'prenom' => $user->user_prenom,
                'email' => $user->user_email,
                'login' => $user->user_login,
                'telephone' => $user->user_tel,
                'email_status' => $user->user_email_status,
                'image' => $user->image_user,
                'etape' => $user->etape,
            ], 'Administrateur récupéré avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération de l\'admin: ' . $e->getMessage());
            
            return $this->error('Une erreur est survenue.', 500);
        }
    }

    /**
     * Supprimer un administrateur
     * 
     * Un admin ne peut pas se supprimer lui-même
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * 
     * @OA\Delete(
     *     path="/admin/users/{id}",
     *     summary="Supprimer un administrateur",
     *     description="Supprime un compte administrateur. Un admin ne peut pas se supprimer lui-même.",
     *     operationId="adminDeleteUser",
     *     tags={"Admin Utilisateurs"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID de l'administrateur", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Administrateur supprimé avec succès"),
     *     @OA\Response(response=403, description="Impossible de supprimer son propre compte"),
     *     @OA\Response(response=404, description="Administrateur non trouvé")
     * )
     */
    public function delete(Request $request, int $id): JsonResponse
    {
        try {
            $currentAdmin = $request->user();

            // Vérifier que l'admin n'essaie pas de se supprimer lui-même
            if ($currentAdmin->id_user == $id) {
                return $this->error('Vous ne pouvez pas supprimer votre propre compte.', 403);
            }

            $user = User::find($id);

            if (!$user) {
                return $this->error('Administrateur non trouvé.', 404);
            }

            // Supprimer l'utilisateur
            $user->delete();

            Log::info('Admin supprimé', [
                'deleted_admin_id' => $id,
                'deleted_by' => $currentAdmin->id_user
            ]);

            return $this->success(null, 'Administrateur supprimé avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de l\'admin: ' . $e->getMessage());
            
            return $this->error('Une erreur est survenue lors de la suppression.', 500);
        }
    }
}
