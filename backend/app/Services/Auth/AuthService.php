<?php

namespace App\Services\Auth;

use App\Models\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Exception;

class AuthService
{
    /**
     * Enregistrer un nouveau client
     *
     * @param array $data
     * @return Client
     * @throws Exception
     */
    public function registerClient(array $data): Client
    {
        DB::beginTransaction();

        try {
            // Créer le client avec les champs exacts de la base
            $client = Client::create([
                'client_nom' => $data['nom'],
                'client_prenom' => $data['prenom'],
                'client_email' => $data['email'],
                'client_login' => $data['login'],
                'client_tel' => $data['telephone'],
                'client_adresse' => $data['adresse'],
                'client_password' => Hash::make($data['password']),
                'image_client' => 'public/avatar.png',
                'client_email_status' => 'vérifier',
                'client_otp' => isset($data['client_otp']) ? (int)$data['client_otp'] : 0,
                'client_jettons' => 5, // 5 jetons gratuits à l'inscription
                'client_jettons_sponsor' => 1, // 1 jeton sponsor gratuit à l'inscription
            ]);

            DB::commit();

            return $client;

        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Erreur lors de l'enregistrement: " . $e->getMessage());
        }
    }

    /**
     * Vérifier si un email existe déjà
     *
     * @param string $email
     * @return bool
     */
    public function emailExists(string $email): bool
    {
        return Client::where('client_email', $email)->exists();
    }

    /**
     * Vérifier si un login existe déjà
     *
     * @param string $login
     * @return bool
     */
    public function loginExists(string $login): bool
    {
        return Client::where('client_login', $login)->exists();
    }

    /**
     * Vérifier si un téléphone existe déjà
     *
     * @param string $telephone
     * @return bool
     */
    public function telephoneExists(string $telephone): bool
    {
        return Client::where('client_tel', $telephone)->exists();
    }

    /**
     * Vérifier les credentials d'un client
     *
     * @param string $identifier Email ou login
     * @param string $password
     * @return Client|null
     */
    public function verifyCredentials(string $identifier, string $password): ?Client
    {
        $client = Client::where('client_email', $identifier)
            ->orWhere('client_login', $identifier)
            ->first();

        if ($client && Hash::check($password, $client->client_password)) {
            return $client;
        }

        return null;
    }

    /**
     * Obtenir un client par ID
     *
     * @param int $id
     * @return Client|null
     */
    public function getClientById(int $id): ?Client
    {
        return Client::find($id);
    }

    /**
     * Mettre à jour les informations d'un client
     *
     * @param Client $client
     * @param array $data
     * @return Client
     */
    public function updateClient(Client $client, array $data): Client
    {
        $updateData = [];

        if (isset($data['nom'])) {
            $updateData['client_nom'] = $data['nom'];
        }

        if (isset($data['prenom'])) {
            $updateData['client_prenom'] = $data['prenom'];
        }

        if (isset($data['telephone'])) {
            $updateData['client_tel'] = $data['telephone'];
        }

        if (isset($data['adresse'])) {
            $updateData['client_adresse'] = $data['adresse'];
        }

        if (!empty($updateData)) {
            $client->update($updateData);
        }

        return $client->fresh();
    }

    /**
     * Changer le mot de passe d'un client
     *
     * @param Client $client
     * @param string $newPassword
     * @return bool
     */
    public function changePassword(Client $client, string $newPassword): bool
    {
        return $client->update([
            'client_password' => Hash::make($newPassword)
        ]);
    }
}
