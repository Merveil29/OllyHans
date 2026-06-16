<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Produit;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use OpenApi\Annotations as OA;

class AdminDashboardController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        try {
            $produits = Produit::selectRaw("
                COUNT(*)               AS total,
                SUM(id_state = 1)      AS en_attente,
                SUM(id_state = 2)      AS publies,
                SUM(id_state = 3)      AS refuses,
                SUM(id_state = 4)      AS premium,
                SUM(id_state = 5)      AS desactives
            ")->first();

            $total_clients = Client::count();

            return $this->success([
                'clients'  => [
                    'total' => $total_clients,
                ],
                'produits' => [
                    'total'      => (int) $produits->total,
                    'en_attente' => (int) $produits->en_attente,
                    'publies'    => (int) $produits->publies,
                    'refuses'    => (int) $produits->refuses,
                    'premium'    => (int) $produits->premium,
                    'desactives' => (int) $produits->desactives,
                ],
            ]);

        } catch (\Throwable $e) {
            Log::error('Erreur dashboard admin: ' . $e->getMessage());
            return $this->serverError($e, 'Erreur lors du chargement du dashboard.');
        }
    }

    public function stats(): JsonResponse
    {
        try {
            $topClients = Client::withCount(['produits' => fn($q) => $q->where('id_state', 2)])
                ->orderByDesc('produits_count')
                ->limit(5)
                ->get()
                ->map(fn($c) => [
                    'id'             => $c->id_client,
                    'nom'            => $c->client_nom . ' ' . $c->client_prenom,
                    'produits_count' => $c->produits_count,
                    'jettons'        => $c->client_jettons,
                ]);

            $totalJettons = Client::sum('client_jettons');

            return $this->success([
                'top_clients'    => $topClients,
                'total_jettons'  => (int) $totalJettons,
            ]);

        } catch (\Throwable $e) {
            Log::error('Erreur stats admin: ' . $e->getMessage());
            return $this->serverError($e);
        }
    }
}
