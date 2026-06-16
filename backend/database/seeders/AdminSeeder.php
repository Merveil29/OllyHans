<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un admin de test
        $adminData = [
            'user_nom' => 'Admin',
            'user_prenom' => 'Test',
            'user_email' => 'jamsport18@gmail.com',
            'user_login' => 'admin',
            'user_password' => Hash::make('Admin@2024'),
            'user_tel' => '0612345678',
            'user_email_status' => 'vérifier', // Email déjà vérifié
            'user_activation_code' => rand(100000, 999999),
            'user_otp' => rand(100000, 999999),
            'image_user' => null,
            'etape' => 1,
        ];

        DB::table('users')->updateOrInsert(
            ['user_email' => $adminData['user_email']],
            $adminData
        );

        $this->command->info('✅ Admin de test créé avec succès !');
        $this->command->info('📧 Email: jamsport18@gmail.com');
        $this->command->info('🔑 Password: Admin@2024');
        $this->command->info('👤 Login: admin');
        $this->command->warn('⚠️  N\'oubliez pas que l\'admin doit vérifier son OTP à chaque connexion !');
    }
}
