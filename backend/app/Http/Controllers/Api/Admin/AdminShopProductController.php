<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\StoreProductRequest;
use App\Http\Requests\Shop\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ShopProductService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class AdminShopProductController extends Controller
{
    use ApiResponse;
    protected ShopProductService $shopProductService;

    public function __construct(ShopProductService $shopProductService)
    {
        $this->shopProductService = $shopProductService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['category_id', 'product_line_id', 'search', 'per_page']);
            $filters['per_page'] = $filters['per_page'] ?? 20;

            $query = Product::with(['productLine', 'category', 'variants'])
                ->orderBy('created_at', 'desc');

            if (!empty($filters['category_id'])) {
                $query->where('category_id', $filters['category_id']);
            }
            if (!empty($filters['product_line_id'])) {
                $query->where('product_line_id', $filters['product_line_id']);
            }
            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }

            $products = $query->paginate($filters['per_page']);

            return $this->paginated($products, function ($product) {
                return (new ProductResource($product))->toArray(request());
            });
        } catch (Exception $e) {
            Log::error('Erreur récupération produits admin: ' . $e->getMessage());
            return $this->serverError($e, 'Erreur lors de la récupération des produits.');
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $product = Product::with(['productLine', 'category', 'variants.stock', 'images'])->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => new ProductResource($product),
            ]);
        } catch (Exception $e) {
            return $this->notFound('Produit non trouvé.');
        }
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            $product = $this->shopProductService->create($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Produit créé avec succès.',
                'data' => new ProductResource($product),
            ], 201);
        } catch (Exception $e) {
            Log::error('Erreur création produit: ' . $e->getMessage());
            return $this->serverError($e, 'Erreur lors de la création du produit.');
        }
    }

    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);
            $product = $this->shopProductService->update($product, $request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Produit mis à jour avec succès.',
                'data' => new ProductResource($product),
            ]);
        } catch (Exception $e) {
            Log::error('Erreur mise à jour produit: ' . $e->getMessage());
            return $this->serverError($e, 'Erreur lors de la mise à jour du produit.');
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);
            $this->shopProductService->delete($product);
            return $this->success(null, 'Produit supprimé avec succès.');
        } catch (Exception $e) {
            Log::error('Erreur suppression produit: ' . $e->getMessage());
            return $this->serverError($e, 'Erreur lors de la suppression du produit.');
        }
    }
}
