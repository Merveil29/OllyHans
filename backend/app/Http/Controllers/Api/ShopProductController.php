<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Services\ShopProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;

class ShopProductController extends Controller
{
    protected ShopProductService $shopProductService;

    public function __construct(ShopProductService $shopProductService)
    {
        $this->shopProductService = $shopProductService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only([
                'category_id', 'product_line_id', 'skin_type',
                'effet', 'search', 'sort', 'direction', 'per_page'
            ]);

            $products = $this->shopProductService->getAll($filters);

            return response()->json([
                'success' => true,
                'data' => ProductResource::collection($products),
                'meta' => [
                    'total' => $products->total(),
                    'per_page' => $products->perPage(),
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'from' => $products->firstItem(),
                    'to' => $products->lastItem(),
                ],
            ]);
        } catch (Exception $e) {
            Log::error('Erreur récupération produits boutique: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des produits.'
            ], 500);
        }
    }

    public function show(string $slug): JsonResponse
    {
        try {
            $product = $this->shopProductService->getBySlug($slug);
            return response()->json([
                'success' => true,
                'data' => new ProductResource($product),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Produit non trouvé.'
            ], 404);
        }
    }

    public function suggestions(Request $request): JsonResponse
    {
        try {
            $search = $request->get('search', '');
            if (strlen($search) < 2) {
                return response()->json(['success' => true, 'data' => []]);
            }

            $products = $this->shopProductService->getAll([
                'search' => $search,
                'per_page' => 10,
            ]);

            return response()->json([
                'success' => true,
                'data' => ProductResource::collection($products),
            ]);
        } catch (Exception $e) {
            Log::error('Erreur suggestions produits: ' . $e->getMessage());
            return response()->json(['success' => false, 'data' => []], 500);
        }
    }
}
