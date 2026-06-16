<?php

namespace App\Services;

use App\Models\ProductVariant;
use App\Models\Stock;
use Exception;

class VariantService
{
    public function create(array $data): ProductVariant
    {
        $variant = ProductVariant::create($data);

        Stock::create([
            'product_variant_id' => $variant->id,
            'quantity' => 0,
            'low_stock_threshold' => 5,
        ]);

        return $variant->fresh(['stock']);
    }

    public function update(ProductVariant $variant, array $data): ProductVariant
    {
        $variant->update($data);
        return $variant->fresh(['stock']);
    }

    public function delete(ProductVariant $variant): void
    {
        if ($variant->stock) {
            $variant->stock->delete();
        }
        $variant->delete();
    }

    public function updateStock(ProductVariant $variant, int $quantity, ?int $threshold = null): Stock
    {
        $stock = $variant->stock;
        if (!$stock) {
            $stock = Stock::create([
                'product_variant_id' => $variant->id,
                'quantity' => $quantity,
                'low_stock_threshold' => $threshold ?? 5,
            ]);
        } else {
            $stock->update([
                'quantity' => $quantity,
                'low_stock_threshold' => $threshold ?? $stock->low_stock_threshold,
            ]);
        }
        return $stock->fresh();
    }
}
