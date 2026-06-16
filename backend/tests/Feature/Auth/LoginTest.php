<?php

namespace Tests\Feature\Auth;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test qu'un client peut se connecter avec son email
     */
    public function test_client_can_login_with_email(): void
    {
        // Créer un client
        $client = Client::create([
            'client_nom' => 'Dupont',
            'client_prenom' => 'Jean',
            'client_email' => 'jean.dupont@example.com',
            'client_login' => 'jeandupont',
            'client_tel' => '+243123456789',
            'client_adresse' => '123 Rue Test',
            'passwords' => Hash::make('Password@123'),
        ]);

        // Tenter de se connecter avec l'email
        $response = $this->postJson('/api/v1/login', [
            'identifier' => 'jean.dupont@example.com',
            'password' => 'Password@123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
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
                        'jettons_sponsor',
                    ],
                    'token',
                ]
            ]);

        $this->assertEquals('Connexion réussie.', $response->json('message'));
        $this->assertNotNull($response->json('data.token'));
    }

    /**
     * Test qu'un client peut se connecter avec son login
     */
    public function test_client_can_login_with_login(): void
    {
        // Créer un client
        $client = Client::create([
            'client_nom' => 'Dupont',
            'client_prenom' => 'Jean',
            'client_email' => 'jean.dupont@example.com',
            'client_login' => 'jeandupont',
            'client_tel' => '+243123456789',
            'client_adresse' => '123 Rue Test',
            'passwords' => Hash::make('Password@123'),
        ]);

        // Tenter de se connecter avec le login
        $response = $this->postJson('/api/v1/login', [
            'identifier' => 'jeandupont',
            'password' => 'Password@123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'client',
                    'token',
                ]
            ]);

        $this->assertEquals('Connexion réussie.', $response->json('message'));
    }

    /**
     * Test que la connexion échoue avec un mauvais mot de passe
     */
    public function test_cannot_login_with_wrong_password(): void
    {
        $client = Client::create([
            'client_nom' => 'Dupont',
            'client_prenom' => 'Jean',
            'client_email' => 'jean.dupont@example.com',
            'client_login' => 'jeandupont',
            'client_tel' => '+243123456789',
            'client_adresse' => '123 Rue Test',
            'passwords' => Hash::make('Password@123'),
        ]);

        $response = $this->postJson('/api/v1/login', [
            'identifier' => 'jean.dupont@example.com',
            'password' => 'WrongPassword123',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Identifiants incorrects.',
            ]);
    }

    /**
     * Test que la connexion échoue avec un email inexistant
     */
    public function test_cannot_login_with_nonexistent_email(): void
    {
        $response = $this->postJson('/api/v1/login', [
            'identifier' => 'nonexistent@example.com',
            'password' => 'Password@123',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Identifiants incorrects.',
            ]);
    }

    /**
     * Test que la connexion échoue avec un login inexistant
     */
    public function test_cannot_login_with_nonexistent_login(): void
    {
        $response = $this->postJson('/api/v1/login', [
            'identifier' => 'nonexistent',
            'password' => 'Password@123',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Identifiants incorrects.',
            ]);
    }

    /**
     * Test que la connexion échoue sans identifier
     */
    public function test_cannot_login_without_identifier(): void
    {
        $response = $this->postJson('/api/v1/login', [
            'password' => 'Password@123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['identifier']);
    }

    /**
     * Test que la connexion échoue sans mot de passe
     */
    public function test_cannot_login_without_password(): void
    {
        $response = $this->postJson('/api/v1/login', [
            'identifier' => 'jeandupont',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test qu'un client peut se déconnecter
     */
    public function test_client_can_logout(): void
    {
        // Créer et authentifier un client
        $client = Client::create([
            'client_nom' => 'Dupont',
            'client_prenom' => 'Jean',
            'client_email' => 'jean.dupont@example.com',
            'client_login' => 'jeandupont',
            'client_tel' => '+243123456789',
            'client_adresse' => '123 Rue Test',
            'passwords' => Hash::make('Password@123'),
        ]);

        $token = $client->createToken('test-token')->plainTextToken;

        // Se déconnecter
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Déconnexion réussie.',
            ]);

        // Vérifier que le token a été supprimé
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $client->id_client,
        ]);
    }

    /**
     * Test que la déconnexion échoue sans authentification
     */
    public function test_cannot_logout_without_authentication(): void
    {
        $response = $this->postJson('/api/v1/logout');

        $response->assertStatus(401);
    }

    /**
     * Test qu'un client connecté reçoit les bonnes données
     */
    public function test_login_returns_correct_client_data(): void
    {
        $client = Client::create([
            'client_nom' => 'Dupont',
            'client_prenom' => 'Jean',
            'client_email' => 'jean.dupont@example.com',
            'client_login' => 'jeandupont',
            'client_tel' => '+243123456789',
            'client_adresse' => '123 Rue Test',
            'passwords' => Hash::make('Password@123'),
            'client_jettons' => 5,
            'client_jettons_sponsor' => 0,
        ]);

        $response = $this->postJson('/api/v1/login', [
            'identifier' => 'jean.dupont@example.com',
            'password' => 'Password@123',
        ]);

        $response->assertStatus(200);

        $clientData = $response->json('data.client');
        
        $this->assertEquals('Dupont', $clientData['nom']);
        $this->assertEquals('Jean', $clientData['prenom']);
        $this->assertEquals('jean.dupont@example.com', $clientData['email']);
        $this->assertEquals('jeandupont', $clientData['login']);
        $this->assertEquals('+243123456789', $clientData['telephone']);
        $this->assertEquals('123 Rue Test', $clientData['adresse']);
        $this->assertEquals(5, $clientData['jettons']);
        $this->assertEquals(0, $clientData['jettons_sponsor']);
    }
}
