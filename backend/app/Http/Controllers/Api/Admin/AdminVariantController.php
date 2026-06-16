<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\StoreVariantRequest;
use App\Http\Requests\Shop\UpdateVariantRequest;
use App\Http\Requests\Shop\UpdateStockRequest;
use App\Http\Resources\ProductVariantResource;
use App\Models\ProductVariant;
use App\Services\VariantService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Exception;

class AdminVariantController extends Controller
{
    use ApiResponse;
    protected VariantService $variantService;

    public function __construct(VariantService $variantService)
    {
        $this->variantService = $variantService;
    }

    public function index(int $productId): JsonResponse
    {
        try {
            $variants = ProductVariant::where('product_id', $productId)
                ->with('stock')
                ->orderBy('type')
                ->orderBy('sort_order')
                ->get();

            return response()->json([
                'success' => true,
                'data' => ProductVariantResource::collection($variants),
            ]);
        } catch (Exception $e) {
            Log::error('Erreur récupération variantes: ' . $e->getMessage());
            return $this->serverError($e, 'Erreur lors de la récupération des variantes.');
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $variant = ProductVariant::with('stock', 'product')->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => new ProductVariantResource($variant),
            ]);
        } catch (Exception $e) {
            return $this->notFound('Variante non trouvée.');
        }
    }

    public function store(StoreVariantRequest $request): JsonResponse
    {
        try {
            $variant = $this->variantService->create($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Variante créée avec succès.',
                'data' => new ProductVariantResource($variant),
            ], 201);
        } catch (Exception $e) {
            Log::error('Erreur création variante: ' . $e->getMessage());
            return $this->serverError($e, 'Erreur lors de la création de la variante.');
        }
    }

    public function update(UpdateVariantRequest $request, int $id): JsonResponse
    {
        try {
            $variant = ProductVariant::findOrFail($id);
            $variant = $this->variantService->update($variant, $request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Variante mise à jour avec succès.',
                'data' => new ProductVariantResource($variant),
            ]);
        } catch (Exception $e) {
            Log::error('Erreur mise à jour variante: ' . $e->getMessage());
            return $this->serverError($e, 'Erreur lors de la mise à jour de la variante.');
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $variant = ProductVariant::findOrFail($id);
            $this->variantService->delete($variant);
            return $this->success(null, 'Variante supprimée avec succès.');
        } catch (Exception $e) {
            Log::error('Erreur suppression variante: ' . $e->getMessage());
            return $this->serverError($e, 'Erreur lors de la suppression de la variante.');
        }
    }

    public function updateStock(UpdateStockRequest $request, int $id): JsonResponse
    {
        try {
            $variant = ProductVariant::findOrFail($id);
            $stock = $this->variantService->updateStock(
                $variant,
                $request->input('quantity'),
                $request->input('low_stock_threshold')
            );
            return response()->json([
                'success' => true,
                'message' => 'Stock mis à jour avec succès.',
                'data' => $stock,
            ]);
        } catch (Exception $e) {
            Log::error('Erreur mise à jour stock: ' . $e->getMessage());
            return $this->serverError($e, 'Erreur lors de la mise à jour du stock.');
        }
    }
}
