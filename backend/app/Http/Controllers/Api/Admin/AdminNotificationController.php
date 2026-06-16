<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produit;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class AdminNotificationController extends Controller
{
    use ApiResponse;

    public function stats(): JsonResponse
    {
        $products = Produit::where('id_state', 1)->count();

        return $this->success([
            'total'    => $products,
            'products' => $products,
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $type    = $request->input('type', 'all');
        $perPage = min((int) $request->input('per_page', 20), 100);
        $page    = max(1, (int) $request->input('page', 1));

        $notifications = collect();

        if (in_array($type, ['all', 'product'])) {
            Produit::where('id_state', 1)
                ->with(['client:id_client,client_nom,client_prenom,client_email'])
                ->orderByDesc('dateSaisie')
                ->orderByDesc('id_produits')
                ->limit(200)
                ->get(['id_produits', 'nom_produits', 'prix_produits', 'id_client', 'dateSaisie'])
                ->each(function ($p) use (&$notifications) {
                    $client = $p->client;
                    $notifications->push([
                        'id'        => 'product_' . $p->id_produits,
                        'type'      => 'product',
                        'priority'  => 'high',
                        'title'     => 'Annonce en attente de validation',
                        'message'   => $p->nom_produits
                            . ($p->prix_produits ? ' — ' . number_format($p->prix_produits, 0, ',', ' ') . ' FCFA' : '')
                            . ($client ? ' · ' . $client->client_nom . ' ' . $client->client_prenom : ''),
                        'sub'       => $client?->client_email,
                        'link'      => '/admin/products',
                        'entity_id' => $p->id_produits,
                        'date'      => $p->dateSaisie,
                        'sort_key'  => ($p->dateSaisie ?? '0000-00-00') . '_' . str_pad($p->id_produits, 10, '0', STR_PAD_LEFT),
                    ]);
                });
        }

        $sorted = $notifications->sortByDesc('sort_key')->values();
        $total  = $sorted->count();
        $items  = $sorted->slice(($page - 1) * $perPage, $perPage)->values();

        return response()->json([
            'success'    => true,
            'data'       => $items->toArray(),
            'pagination' => [
                'total'        => $total,
                'per_page'     => $perPage,
                'current_page' => $page,
                'last_page'    => max(1, (int) ceil($total / $perPage)),
            ],
        ]);
    }
}
