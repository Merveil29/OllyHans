<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductLine;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $soinsCategory = Category::where('slug', 'gammes-de-soins')->first();
        $horsGammeCategory = Category::where('slug', 'produits-hors-gamme')->first();

        $productTypes = ['Lait', 'Gel', 'Savon Corps', 'Gommage', 'Lotion', 'Crème Visage', 'Savon Visage'];

        // Create products for each line
        $lines = ProductLine::all();
        foreach ($lines as $line) {
            foreach ($productTypes as $type) {
                $name = "$type $line->name";
                Product::create([
                    'product_line_id' => $line->id,
                    'category_id' => $soinsCategory->id,
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'description' => "$type de la gamme $line->name. " . ($line->description ?: ''),
                    'bienfaits' => "$type aux bienfaits " . strtolower($line->effet ?: 'naturels') . " pour la peau",
                    'composition' => "Composition à base de " . ($line->base_ingredient ?: 'ingrédients naturels'),
                    'skin_type' => $line->skin_type,
                    'effet' => $line->effet,
                    'is_active' => true,
                    'sort_order' => array_search($type, $productTypes) + 1,
                ]);
            }
        }

        // Hors gamme products
        $horsGammeProducts = [
            ['name' => 'Savon Passion Blanchissant Huileux', 'slug' => 'savon-passion-blanchissant-huileux', 'skin_type' => 'Peaux grasses', 'effet' => 'Blanchissant'],
            ['name' => 'Savon Papaya Blanchissant Intense Huileux', 'slug' => 'savon-papaya-blanchissant-intense-huileux', 'skin_type' => 'Peaux grasses', 'effet' => 'Blanchissant intense'],
            ['name' => 'Lait Éclaircissant 100% Botanique', 'slug' => 'lait-eclaircissant-100-botanique', 'skin_type' => 'Tous types de peau', 'effet' => 'Éclaircissant'],
            ['name' => 'Chantilly de Beurre Cica Glow', 'slug' => 'chantilly-beurre-cica-glow', 'skin_type' => 'Peaux sèches', 'effet' => 'Réparateur et glow'],
            ['name' => 'Gel Douche Lait de Neige', 'slug' => 'gel-douche-lait-de-neige', 'skin_type' => 'Tous types de peau', 'effet' => 'Nettoyant doux'],
            ['name' => 'Lotion Visage Herbal', 'slug' => 'lotion-visage-herbal', 'skin_type' => 'Tous types de peau', 'effet' => 'Purifiant'],
            ['name' => 'Lait Soie d\'Urgence', 'slug' => 'lait-soie-urgence', 'skin_type' => 'Peaux très sèches', 'effet' => 'Réparateur intensif'],
            ['name' => 'Savon Nila', 'slug' => 'savon-nila', 'skin_type' => 'Tous types de peau', 'effet' => 'Nettoyant naturel'],
            ['name' => 'Savon Noir Activateur d\'Éclat', 'slug' => 'savon-noir-activateur-eclat', 'skin_type' => 'Tous types de peau', 'effet' => 'Active l\'éclat'],
            ['name' => 'Savon Noir Effet Immédiat', 'slug' => 'savon-noir-effet-immediat', 'skin_type' => 'Tous types de peau', 'effet' => 'Éclat immédiat'],
        ];

        foreach ($horsGammeProducts as $index => $product) {
            Product::create([
                'product_line_id' => null,
                'category_id' => $horsGammeCategory->id,
                'name' => $product['name'],
                'slug' => $product['slug'],
                'description' => $product['name'] . ' - Soin cosmétique de qualité.',
                'bienfaits' => 'Bienfaits ' . ($product['effet'] ?? 'naturels') . ' pour ' . ($product['skin_type'] ?? 'la peau'),
                'composition' => 'Ingrédients naturels soigneusement sélectionnés',
                'skin_type' => $product['skin_type'] ?? 'Tous types de peau',
                'effet' => $product['effet'] ?? 'Naturel',
                'is_active' => true,
                'sort_order' => $index + 1,
            ]);
        }
    }
}
