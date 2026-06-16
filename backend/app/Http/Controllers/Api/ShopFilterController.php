<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Support\Facades\Log;

class ShopFilterController extends Controller
{
    public function filters(): JsonResponse
    {
        try {
            $skinTypes = Product::where('is_active', true)
                ->whereNotNull('skin_type')
                ->distinct()
                ->pluck('skin_type')
                ->map(function ($item) {
                    return ['value' => $item, 'label' => $item];
                })
                ->values();

            $effets = Product::where('is_active', true)
                ->whereNotNull('effet')
                ->distinct()
                ->pluck('effet')
                ->map(function ($item) {
                    return ['value' => $item, 'label' => $item];
                })
                ->values();

            return response()->json([
                'success' => true,
                'data' => [
                    'skin_types' => $skinTypes,
                    'effets' => $effets,
                ],
            ]);
        } catch (Exception $e) {
            Log::error('Erreur récupération filtres: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des filtres.'
            ], 500);
        }
    }
}
