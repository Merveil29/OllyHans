<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Gammes de soins',
                'slug' => 'gammes-de-soins',
                'description' => 'Nos gammes complètes de soins cosmétiques pour une beauté naturelle et éclatante',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Produits hors gamme',
                'slug' => 'produits-hors-gamme',
                'description' => 'Produits cosmétiques indépendants, en dehors de nos gammes principales',
                'is_active' => true,
                'sort_order' => 2,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
