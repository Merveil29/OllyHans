<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     * 
     * ⚠️ ORDRE IMPORTANT : Respecter les dépendances (foreign keys)
     * Données synchronisées avec la base de production (dwsaqreq_topidealspace.sql)
     */
    public function run(): void
    {
        $this->command->info('🚀 Démarrage du seeding de la base de données...');

        // Ordre d'exécution CRITIQUE pour respecter les foreign keys
        $this->call([
            // 1. Tables de référence (sans dépendances)
            CategorieSeeder::class,
            
            // 2. Utilisateurs admin
            AdminSeeder::class,
            
            // 3. Catégories cosmétiques
            CategorySeeder::class,
            
            // 4. Gammes de produits
            ProductLineSeeder::class,
            
            // 5. Produits
            ProductSeeder::class,
            
            // 6. Variantes et stocks
            VariantSeeder::class,
        ]);

        $this->command->info('✅ Base de données seedée avec succès !');
        $this->command->info('📊 Données importées :');
        $this->command->info('   - 5 états (state)');
        $this->command->info('   - 3 états de tickets (statetickets)');
        $this->command->info('   - 7 catégories');
        $this->command->info('   - Utilisateurs admin');
        $this->command->info('   - Catégories cosmétiques');
        $this->command->info('   - 6 gammes de soins');
        $this->command->info('   - Produits et variantes');
    }
}
