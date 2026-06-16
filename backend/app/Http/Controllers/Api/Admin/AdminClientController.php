<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use OpenApi\Annotations as OA;

class AdminClientController extends Controller
{
    use ApiResponse;

    /**
     * Liste tous les clients
     *
     * @return JsonResponse
     * 
     * @OA\Get(
     *     path="/admin/clients",
     *     summary="Liste des clients",
     *     description="Récupère la liste paginée de tous les clients avec le nombre de produits et sponsors",
     *     operationId="adminGetClients",
     *     tags={"Admin Clients"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="search", in="query", description="Rechercher par nom, prénom, email ou login", @OA\Schema(type="string")),
     *     @OA\Parameter(name="per_page", in="query", description="Nombre d'éléments par page", @OA\Schema(type="integer", default=20)),
     *     @OA\Response(response=200, description="Liste paginée des clients", @OA\JsonContent(ref="#/components/schemas/PaginatedResponse")),
     *     @OA\Response(response=401, description="Non authentifié"),
     *     @OA\Response(response=403, description="Accès refusé")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Client::withCount(['produits'])
                ->orderBy('id_client', 'desc');

            if ($request->filled('search')) {
                $s = $request->search;
                $query->where(function ($q) use ($s) {
                    $q->where('client_nom', 'LIKE', "%{$s}%")
                      ->orWhere('client_prenom', 'LIKE', "%{$s}%")
                      ->orWhere('client_email', 'LIKE', "%{$s}%")
                      ->orWhere('client_login', 'LIKE', "%{$s}%");
                });
            }

            $clients = $query->paginate($request->input('per_page', 20));

            return $this->paginated($clients, fn($c) => (new ClientResource($c))->toArray($request));

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des clients: ' . $e->getMessage());
            return $this->serverError($e);
        }
    }

    /**
     * Voir les détails d'un client
     *
     * @param int $id
     * @return JsonResponse
     * 
     * @OA\Get(
     *     path="/admin/clients/{id}",
     *     summary="Détails d'un client",
     *     description="Récupère les informations détaillées d'un client spécifique",
     *     operationId="adminGetClient",
     *     tags={"Admin Clients"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID du client", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Détails du client", @OA\JsonContent(@OA\Property(property="success", type="boolean"), @OA\Property(property="data", ref="#/components/schemas/Client"))),
     *     @OA\Response(response=404, description="Client non trouvé")
     * )
     */
    public function show(int $id): JsonResponse
    {
        try {
            $client = Client::withCount(['produits'])->find($id);

            if (!$client) {
                return $this->notFound('Client non trouvé.');
            }

            return $this->success(new ClientResource($client));

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération du client: ' . $e->getMessage());
            return $this->serverError($e);
        }
    }

    /**
     * Supprimer un client et toutes ses données associées (produits et sponsors)
     *
     * @param int $id
     * @return JsonResponse
     * 
     * @OA\Delete(
     *     path="/admin/clients/{id}",
     *     summary="Supprimer un client",
     * description="Supprime un client et toutes ses données associées (produits)",
     *     operationId="adminDeleteClient",
     *     tags={"Admin Clients"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID du client", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Client supprimé avec succès", @OA\JsonContent(@OA\Property(property="success", type="boolean"), @OA\Property(property="data", type="object", @OA\Property(property="produits_supprimes", type="integer")))),
     *     @OA\Response(response=404, description="Client non trouvé")
     * )
     */
    public function delete(int $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $client = Client::find($id);

            if (!$client) {
                return $this->notFound('Client non trouvé.');
            }

            $produitsCount = $client->produits()->count();

            $client->produits()->delete();

            // Supprimer le client
            $client->delete();

            DB::commit();

            Log::info('Client supprimé avec ses données associées', [
                'client_id' => $id,
                'produits_supprimes' => $produitsCount,
            ]);

            return $this->success([
                'produits_supprimes' => $produitsCount,
            ], 'Client supprimé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la suppression du client: ' . $e->getMessage());
            return $this->serverError($e);
        }
    }

    /**
     * Mettre à jour les jetons d'un client
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * 
     * @OA\Put(
     *     path="/admin/clients/{id}/jetons",
     *     summary="Mettre à jour les jetons d'un client",
     *     description="Modifie le nombre de jetons produits et/ou jetons sponsor d'un client",
     *     operationId="adminUpdateClientJetons",
     *     tags={"Admin Clients"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID du client", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="jettons", type="integer", minimum=0, description="Nombre de jetons produits"),
     *             @OA\Property(property="jettons_sponsor", type="integer", minimum=0, description="Nombre de jetons sponsor")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Jetons mis à jour", @OA\JsonContent(@OA\Property(property="success", type="boolean"), @OA\Property(property="data", type="object", @OA\Property(property="jettons", type="integer"), @OA\Property(property="jettons_sponsor", type="integer")))),
     *     @OA\Response(response=404, description="Client non trouvé"),
     *     @OA\Response(response=422, description="Erreur de validation")
     * )
     */
    public function updateJetons(Request $request, int $id): JsonResponse
    {
        try {
            $client = Client::find($id);

            if (!$client) {
                return $this->notFound('Client non trouvé.');
            }

            $validated = $request->validate([
                'jettons' => 'nullable|integer|min:0',
                'jettons_sponsor' => 'nullable|integer|min:0',
            ]);

            if (isset($validated['jettons'])) {
                $client->client_jettons = $validated['jettons'];
            }

            if (isset($validated['jettons_sponsor'])) {
                $client->client_jettons_sponsor = $validated['jettons_sponsor'];
            }

            $client->save();

            Log::info('Jetons client mis à jour', [
                'client_id' => $id,
                'jettons' => $client->client_jettons,
                'jettons_sponsor' => $client->client_jettons_sponsor,
            ]);

            return $this->success([
                'jettons' => $client->client_jettons,
                'jettons_sponsor' => $client->client_jettons_sponsor,
            ], 'Jetons mis à jour avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour des jetons: ' . $e->getMessage());
            return $this->serverError($e);
        }
    }
}
