<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ProductLine;
use Illuminate\Database\Seeder;

class ProductLineSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::where('slug', 'gammes-de-soins')->first();

        $lines = [
            [
                'category_id' => $category->id,
                'name' => 'Balance Skin',
                'slug' => 'balance-skin',
                'description' => 'Gamme équilibrante à base d\'hibiscus pour tous les types de peau',
                'short_description' => 'Équilibre et éclat naturel',
                'color_name' => 'Marron clair',
                'color_code' => 'Rouge',
                'base_ingredient' => 'Hibiscus',
                'effet' => 'Équilibrant',
                'skin_type' => 'Tous types de peau',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'category_id' => $category->id,
                'name' => 'Golden Éclat',
                'slug' => 'golden-eclat',
                'description' => 'Gamme éclaircissante à la mangue pour un teint jaune métissé lumineux',
                'short_description' => 'Éclat jaune métissé',
                'color_name' => null,
                'color_code' => 'Jaune',
                'base_ingredient' => 'Mangue',
                'effet' => 'Éclaircissant jaune métisse',
                'skin_type' => 'Peaux métissées',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'category_id' => $category->id,
                'name' => '5D Molato',
                'slug' => '5d-molato',
                'description' => 'Gamme blanchissante douce à la papaye pour les peaux sensibles',
                'short_description' => 'Blanchissant peaux sensibles',
                'color_name' => null,
                'color_code' => 'Orange',
                'base_ingredient' => 'Papaye',
                'effet' => 'Blanchissant peau sensible',
                'skin_type' => 'Peaux sensibles',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'category_id' => $category->id,
                'name' => 'Lumia Collagen',
                'slug' => 'lumia-collagen',
                'description' => 'Gamme blanchissante réparatrice au collagène pour une peau jeune jusqu\'à 70 ans',
                'short_description' => 'Blanchissant réparateur',
                'color_name' => null,
                'color_code' => 'Rose',
                'base_ingredient' => 'Collagène',
                'effet' => 'Blanchissant réparateur',
                'skin_type' => 'Peaux matures (jusqu\'à 70 ans)',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'category_id' => $category->id,
                'name' => 'Nigériane Glowing Effet Immédiat',
                'slug' => 'nigeriane-glowing-effet-immediat',
                'description' => 'Gamme universelle au savon noir qui augmente la teinte selon la carnation de départ',
                'short_description' => 'Universel, effet immédiat',
                'color_name' => null,
                'color_code' => null,
                'base_ingredient' => 'Savon noir',
                'effet' => 'Universel, augmente la teinte selon la carnation',
                'skin_type' => 'Tous types de peau',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'category_id' => $category->id,
                'name' => 'Naija',
                'slug' => 'naija',
                'description' => 'Gamme peeling réparateur pour peaux difficiles. Idéal pour masque de grossesse. Interdit aux peaux sèches.',
                'short_description' => 'Peeling réparateur peaux difficiles',
                'color_name' => null,
                'color_code' => null,
                'base_ingredient' => 'Savon noir peeling',
                'effet' => 'Peeling réparateur',
                'skin_type' => 'Peaux difficiles (interdit peaux sèches)',
                'is_active' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($lines as $line) {
            ProductLine::create($line);
        }
    }
}
