<?php

namespace App\Services;

use App\Models\ProductLine;
use Exception;
use Illuminate\Support\Str;

class ProductLineService
{
    public function getAll(bool $activeOnly = false)
    {
        $query = ProductLine::with('category')->withCount('products');
        if ($activeOnly) {
            $query->where('is_active', true);
        }
        return $query->orderBy('sort_order')->get();
    }

    public function getBySlug(string $slug): ProductLine
    {
        return ProductLine::where('slug', $slug)
            ->with(['products' => function ($q) {
                $q->where('is_active', true)->orderBy('sort_order');
            }, 'products.variants' => function ($q) {
                $q->where('is_active', true);
            }, 'products.variants.stock', 'products.images'])
            ->firstOrFail();
    }

    public function create(array $data): ProductLine
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        return ProductLine::create($data);
    }

    public function update(ProductLine $productLine, array $data): ProductLine
    {
        if (isset($data['name']) && !isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $productLine->update($data);
        return $productLine->fresh();
    }

    public function delete(ProductLine $productLine): void
    {
        if ($productLine->products()->exists()) {
            throw new Exception('Impossible de supprimer une gamme qui contient des produits.');
        }
        $productLine->delete();
    }
}
