<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Stock;
use Illuminate\Database\Seeder;

class VariantSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();

        $retailVariants = [
            ['name' => 'Petit format', 'size_label' => '200ml', 'sort_order' => 1],
            ['name' => 'Moyen format', 'size_label' => '500ml', 'sort_order' => 2],
            ['name' => 'Grand format', 'size_label' => '1L', 'sort_order' => 3],
        ];

        $wholesaleVariants = [
            ['name' => '5 kg', 'size_label' => '5kg', 'sort_order' => 1],
            ['name' => '10 kg', 'size_label' => '10kg', 'sort_order' => 2],
            ['name' => '20 kg', 'size_label' => '20kg', 'sort_order' => 3],
            ['name' => '25 kg', 'size_label' => '25kg', 'sort_order' => 4],
            ['name' => '50 kg', 'size_label' => '50kg', 'sort_order' => 5],
            ['name' => '100 kg', 'size_label' => '100kg', 'sort_order' => 6],
        ];

        foreach ($products as $product) {
            // Retail variants
            foreach ($retailVariants as $i => $variant) {
                $price = $this->getRetailPrice($product, $i);
                $v = ProductVariant::create([
                    'product_id' => $product->id,
                    'type' => 'retail',
                    'name' => $variant['name'],
                    'size_label' => $variant['size_label'],
                    'price' => $price,
                    'weight' => $i === 0 ? 0.2 : ($i === 1 ? 0.5 : 1.0),
                    'is_active' => true,
                    'sort_order' => $variant['sort_order'],
                ]);
                Stock::create([
                    'product_variant_id' => $v->id,
                    'quantity' => 50,
                    'low_stock_threshold' => 5,
                ]);
            }

            // Wholesale variants
            foreach ($wholesaleVariants as $i => $variant) {
                $price = $this->getWholesalePrice($product, $i);
                $v = ProductVariant::create([
                    'product_id' => $product->id,
                    'type' => 'wholesale',
                    'name' => $variant['name'],
                    'size_label' => $variant['size_label'],
                    'price' => $price,
                    'weight' => [5, 10, 20, 25, 50, 100][$i],
                    'is_active' => true,
                    'sort_order' => $variant['sort_order'],
                ]);
                Stock::create([
                    'product_variant_id' => $v->id,
                    'quantity' => 100,
                    'low_stock_threshold' => 10,
                ]);
            }
        }
    }

    private function getRetailPrice($product, int $index): float
    {
        $basePrices = [
            'Lait' => [2500, 4500, 7500],
            'Gel' => [2000, 3500, 6000],
            'Savon Corps' => [1500, 2500, 4500],
            'Gommage' => [3000, 5000, 8500],
            'Lotion' => [2500, 4000, 7000],
            'Crème Visage' => [3500, 6000, 10000],
            'Savon Visage' => [1500, 2500, 4000],
        ];

        foreach ($basePrices as $key => $prices) {
            if (str_starts_with($product->name, $key)) {
                return $prices[$index];
            }
        }

        return [3000, 5000, 8500][$index];
    }

    private function getWholesalePrice($product, int $index): float
    {
        $sizes = [5, 10, 20, 25, 50, 100];

        $mediumRetail = $product->variants()
            ->where('type', 'retail')
            ->where('sort_order', 2)
            ->first();

        $base = $mediumRetail ? $mediumRetail->price : 5000;
        $pricePerUnit = $base / 0.5;

        return round($pricePerUnit * $sizes[$index] * 0.7, -2);
    }
}
