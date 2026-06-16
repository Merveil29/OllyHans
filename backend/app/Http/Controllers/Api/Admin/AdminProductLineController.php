<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\StoreProductLineRequest;
use App\Http\Requests\Shop\UpdateProductLineRequest;
use App\Http\Resources\ProductLineResource;
use App\Models\ProductLine;
use App\Services\ProductLineService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Exception;

class AdminProductLineController extends Controller
{
    use ApiResponse;
    protected ProductLineService $productLineService;

    public function __construct(ProductLineService $productLineService)
    {
        $this->productLineService = $productLineService;
    }

    public function index(): JsonResponse
    {
        try {
            $lines = $this->productLineService->getAll();
            return response()->json([
                'success' => true,
                'data' => ProductLineResource::collection($lines),
            ]);
        } catch (Exception $e) {
            Log::error('Erreur récupération gammes admin: ' . $e->getMessage());
            return $this->serverError($e, 'Erreur lors de la récupération des gammes.');
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $line = ProductLine::with(['products.variants.stock', 'products.images', 'category'])->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => new ProductLineResource($line),
            ]);
        } catch (Exception $e) {
            return $this->notFound('Gamme non trouvée.');
        }
    }

    public function store(StoreProductLineRequest $request): JsonResponse
    {
        try {
            $line = $this->productLineService->create($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Gamme créée avec succès.',
                'data' => new ProductLineResource($line),
            ], 201);
        } catch (Exception $e) {
            Log::error('Erreur création gamme: ' . $e->getMessage());
            return $this->serverError($e, 'Erreur lors de la création de la gamme.');
        }
    }

    public function update(UpdateProductLineRequest $request, int $id): JsonResponse
    {
        try {
            $line = ProductLine::findOrFail($id);
            $line = $this->productLineService->update($line, $request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Gamme mise à jour avec succès.',
                'data' => new ProductLineResource($line),
            ]);
        } catch (Exception $e) {
            Log::error('Erreur mise à jour gamme: ' . $e->getMessage());
            return $this->serverError($e, 'Erreur lors de la mise à jour de la gamme.');
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $line = ProductLine::findOrFail($id);
            $this->productLineService->delete($line);
            return $this->success(null, 'Gamme supprimée avec succès.');
        } catch (Exception $e) {
            Log::error('Erreur suppression gamme: ' . $e->getMessage());
            return $this->serverError($e, $e->getMessage() ?: 'Erreur lors de la suppression.');
        }
    }
}
