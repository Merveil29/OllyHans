<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductLineResource;
use App\Services\ProductLineService;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Support\Facades\Log;

class ProductLineController extends Controller
{
    protected ProductLineService $productLineService;

    public function __construct(ProductLineService $productLineService)
    {
        $this->productLineService = $productLineService;
    }

    public function index(): JsonResponse
    {
        try {
            $lines = $this->productLineService->getAll(true);
            return response()->json([
                'success' => true,
                'data' => ProductLineResource::collection($lines),
            ]);
        } catch (Exception $e) {
            Log::error('Erreur récupération gammes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des gammes.'
            ], 500);
        }
    }

    public function show(string $slug): JsonResponse
    {
        try {
            $line = $this->productLineService->getBySlug($slug);
            return response()->json([
                'success' => true,
                'data' => new ProductLineResource($line),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gamme non trouvée.'
            ], 404);
        }
    }
}
