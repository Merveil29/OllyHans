<?php

namespace App\Services;

use App\Models\Category;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CategoryService
{
    public function getAll(bool $activeOnly = false)
    {
        $query = Category::withCount(['productLines', 'products']);
        if ($activeOnly) {
            $query->where('is_active', true);
        }
        return $query->orderBy('sort_order')->get();
    }

    public function getBySlug(string $slug): Category
    {
        return Category::where('slug', $slug)
            ->with(['productLines' => function ($q) {
                $q->where('is_active', true)->orderBy('sort_order');
            }])
            ->withCount('products')
            ->firstOrFail();
    }

    public function create(array $data): Category
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        return Category::create($data);
    }

    public function update(Category $category, array $data): Category
    {
        if (isset($data['name']) && !isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $category->update($data);
        return $category->fresh();
    }

    public function delete(Category $category): void
    {
        if ($category->productLines()->exists()) {
            throw new Exception('Impossible de supprimer une catégorie qui contient des gammes.');
        }
        $category->delete();
    }
}
