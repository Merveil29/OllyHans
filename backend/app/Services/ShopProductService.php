<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Stock;
use Exception;
use Illuminate\Support\Str;

class ShopProductService
{
    public function getAll(array $filters = [])
    {
        $query = Product::with(['productLine', 'category', 'variants' => function ($q) {
            $q->where('is_active', true);
        }, 'variants.stock', 'images'])->where('is_active', true);

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['product_line_id'])) {
            $query->where('product_line_id', $filters['product_line_id']);
        }

        if (!empty($filters['skin_type'])) {
            $query->where('skin_type', 'LIKE', '%' . $filters['skin_type'] . '%');
        }

        if (!empty($filters['effet'])) {
            $query->where('effet', 'LIKE', '%' . $filters['effet'] . '%');
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('bienfaits', 'LIKE', "%{$search}%");
            });
        }

        $sortField = $filters['sort'] ?? 'sort_order';
        $sortDir = $filters['direction'] ?? 'asc';
        $allowedSorts = ['name', 'created_at', 'sort_order'];
        $sortField = in_array($sortField, $allowedSorts) ? $sortField : 'sort_order';

        return $query->orderBy($sortField, $sortDir)->paginate($filters['per_page'] ?? 12);
    }

    public function getBySlug(string $slug): Product
    {
        return Product::where('slug', $slug)
            ->where('is_active', true)
            ->with(['productLine', 'category', 'variants' => function ($q) {
                $q->where('is_active', true)->orderBy('sort_order');
            }, 'variants.stock', 'images' => function ($q) {
                $q->orderBy('sort_order');
            }])
            ->firstOrFail();
    }

    public function create(array $data): Product
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        if (isset($data['name']) && !isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $product->update($data);
        return $product->fresh();
    }

    public function delete(Product $product): void
    {
        $product->variants()->delete();
        $product->images()->delete();
        $product->delete();
    }

    public function getRetailVariants(Product $product)
    {
        return $product->variants()
            ->where('type', 'retail')
            ->where('is_active', true)
            ->with('stock')
            ->orderBy('sort_order')
            ->get();
    }

    public function getWholesaleVariants(Product $product)
    {
        return $product->variants()
            ->where('type', 'wholesale')
            ->where('is_active', true)
            ->with('stock')
            ->orderBy('sort_order')
            ->get();
    }
}
