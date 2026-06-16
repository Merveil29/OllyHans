<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(): JsonResponse
    {
        try {
            $categories = $this->categoryService->getAll(true);
            return response()->json([
                'success' => true,
                'data' => CategoryResource::collection($categories),
            ]);
        } catch (Exception $e) {
            Log::error('Erreur récupération catégories: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des catégories.'
            ], 500);
        }
    }

    public function show(string $slug): JsonResponse
    {
        try {
            $category = $this->categoryService->getBySlug($slug);
            return response()->json([
                'success' => true,
                'data' => new CategoryResource($category),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Catégorie non trouvée.'
            ], 404);
        }
    }
}
