<?php

namespace Tests\Feature\Auth;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test successful client registration
     */
    public function test_client_can_register_successfully(): void
    {
        $response = $this->postJson('/api/v1/register', [
            'nom' => 'Ndiaye',
            'prenom' => 'Moussa',
            'email' => 'moussa.ndiaye@example.com',
            'login' => 'moussandiaye',
            'telephone' => '+221771234567',
            'adresse' => 'Dakar, Plateau',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'client' => [
                        'id',
                        'nom',
                        'prenom',
                        'email',
                        'login',
                        'telephone',
                        'adresse',
                        'jettons',
                        'jettons_sponsor'
                    ],
                    'token',
                    'token_type'
                ]
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'client' => [
                        'nom' => 'Ndiaye',
                        'prenom' => 'Moussa',
                        'email' => 'moussa.ndiaye@example.com',
                        'login' => 'moussandiaye',
                        'jettons' => 5,
                        'jettons_sponsor' => 0
                    ]
                ]
            ]);

        // Vérifier que le client est créé en base de données
        $this->assertDatabaseHas('clients', [
            'client_email' => 'moussa.ndiaye@example.com',
            'client_login' => 'moussandiaye',
            'client_jettons' => 5
        ]);
    }

    /**
     * Test registration with duplicate email
     */
    public function test_cannot_register_with_duplicate_email(): void
    {
        // Créer un client existant
        Client::create([
            'client_nom' => 'Test',
            'client_prenom' => 'User',
            'client_email' => 'existing@example.com',
            'client_login' => 'existinguser',
            'client_tel' => '+221771111111',
            'client_adresse' => 'Dakar',
            'passwords' => bcrypt('password'),
            'client_jettons' => 5,
            'client_jettons_sponsor' => 0
        ]);

        $response = $this->postJson('/api/v1/register', [
            'nom' => 'Ndiaye',
            'prenom' => 'Moussa',
            'email' => 'existing@example.com', // Email déjà utilisé
            'login' => 'moussandiaye',
            'telephone' => '+221772222222',
            'adresse' => 'Dakar, Plateau',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test registration with duplicate login
     */
    public function test_cannot_register_with_duplicate_login(): void
    {
        Client::create([
            'client_nom' => 'Test',
            'client_prenom' => 'User',
            'client_email' => 'test@example.com',
            'client_login' => 'existinglogin',
            'client_tel' => '+221771111111',
            'client_adresse' => 'Dakar',
            'passwords' => bcrypt('password'),
            'client_jettons' => 5,
            'client_jettons_sponsor' => 0
        ]);

        $response = $this->postJson('/api/v1/register', [
            'nom' => 'Ndiaye',
            'prenom' => 'Moussa',
            'email' => 'moussa@example.com',
            'login' => 'existinglogin', // Login déjà utilisé
            'telephone' => '+221772222222',
            'adresse' => 'Dakar, Plateau',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['login']);
    }

    /**
     * Test registration with weak password
     */
    public function test_cannot_register_with_weak_password(): void
    {
        $response = $this->postJson('/api/v1/register', [
            'nom' => 'Ndiaye',
            'prenom' => 'Moussa',
            'email' => 'moussa@example.com',
            'login' => 'moussandiaye',
            'telephone' => '+221771234567',
            'adresse' => 'Dakar, Plateau',
            'password' => 'weak', // Mot de passe trop faible
            'password_confirmation' => 'weak'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test registration with password mismatch
     */
    public function test_cannot_register_with_password_mismatch(): void
    {
        $response = $this->postJson('/api/v1/register', [
            'nom' => 'Ndiaye',
            'prenom' => 'Moussa',
            'email' => 'moussa@example.com',
            'login' => 'moussandiaye',
            'telephone' => '+221771234567',
            'adresse' => 'Dakar, Plateau',
            'password' => 'Password@123',
            'password_confirmation' => 'DifferentPassword@123' // Confirmation différente
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test registration with missing required fields
     */
    public function test_cannot_register_with_missing_fields(): void
    {
        $response = $this->postJson('/api/v1/register', [
            'nom' => 'Ndiaye',
            // Champs manquants
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'prenom',
                'email',
                'login',
                'telephone',
                'adresse',
                'password'
            ]);
    }

    /**
     * Test registration with invalid email format
     */
    public function test_cannot_register_with_invalid_email(): void
    {
        $response = $this->postJson('/api/v1/register', [
            'nom' => 'Ndiaye',
            'prenom' => 'Moussa',
            'email' => 'invalid-email', // Format invalide
            'login' => 'moussandiaye',
            'telephone' => '+221771234567',
            'adresse' => 'Dakar, Plateau',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test check email availability
     */
    public function test_can_check_email_availability(): void
    {
        // Email disponible
        $response = $this->getJson('/api/v1/check-email/newemail@example.com');
        $response->assertStatus(200)
            ->assertJson([
                'available' => true
            ]);

        // Email déjà utilisé
        Client::create([
            'client_nom' => 'Test',
            'client_prenom' => 'User',
            'client_email' => 'used@example.com',
            'client_login' => 'testuser',
            'client_tel' => '+221771111111',
            'client_adresse' => 'Dakar',
            'passwords' => bcrypt('password'),
            'client_jettons' => 5,
            'client_jettons_sponsor' => 0
        ]);

        $response = $this->getJson('/api/v1/check-email/used@example.com');
        $response->assertStatus(200)
            ->assertJson([
                'available' => false
            ]);
    }

    /**
     * Test check login availability
     */
    public function test_can_check_login_availability(): void
    {
        // Login disponible
        $response = $this->getJson('/api/v1/check-login/newlogin');
        $response->assertStatus(200)
            ->assertJson([
                'available' => true
            ]);

        // Login déjà utilisé
        Client::create([
            'client_nom' => 'Test',
            'client_prenom' => 'User',
            'client_email' => 'test@example.com',
            'client_login' => 'usedlogin',
            'client_tel' => '+221771111111',
            'client_adresse' => 'Dakar',
            'passwords' => bcrypt('password'),
            'client_jettons' => 5,
            'client_jettons_sponsor' => 0
        ]);

        $response = $this->getJson('/api/v1/check-login/usedlogin');
        $response->assertStatus(200)
            ->assertJson([
                'available' => false
            ]);
    }
}
