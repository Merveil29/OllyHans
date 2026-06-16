<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * ⚠️ ATTENTION : Ces données doivent correspondre EXACTEMENT à la base de production
     * Fichier source: dwsaqreq_topidealspace.sql
     * Ces 7 catégories sont utilisées en production avec des images spécifiques
     * NE PAS MODIFIER sans vérifier la production !
     */
    public function run(): void
    {
        // Catégories cosmétiques - Marketplace
        $categories = [
            [
                'id_categorie' => 1,
                'nom_categorie' => 'Soins Visage',
                'image_categorie' => 'categorie/cosmetique_visage.png'
            ],
            [
                'id_categorie' => 2,
                'nom_categorie' => 'Soins Corps',
                'image_categorie' => 'categorie/cosmetique_corps.png'
            ],
            [
                'id_categorie' => 3,
                'nom_categorie' => 'Soins Cheveux',
                'image_categorie' => 'categorie/cosmetique_cheveux.png'
            ],
            [
                'id_categorie' => 4,
                'nom_categorie' => 'Maquillage',
                'image_categorie' => 'categorie/cosmetique_maquillage.png'
            ],
            [
                'id_categorie' => 5,
                'nom_categorie' => 'Parfums',
                'image_categorie' => 'categorie/cosmetique_parfums.png'
            ],
            [
                'id_categorie' => 6,
                'nom_categorie' => 'Hygiène & Bain',
                'image_categorie' => 'categorie/cosmetique_hygenie.png'
            ],
            [
                'id_categorie' => 7,
                'nom_categorie' => 'Bien-être & Naturel',
                'image_categorie' => 'categorie/cosmetique_bienetre.png'
            ],
        ];

        foreach ($categories as $categorie) {
            DB::table('categorie')->updateOrInsert(
                ['id_categorie' => $categorie['id_categorie']],
                $categorie
            );
        }
    }
}
