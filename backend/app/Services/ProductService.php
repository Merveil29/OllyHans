<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProductPendingValidation;
use App\Mail\ProductValidated;
use App\Mail\ProductRejected;
use App\Jobs\SendNewsletterJob;
use Exception;

class ProductService
{
    protected CloudinaryService $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    /**
     * Créer un produit pour un client
     * Vérifie les jetons, upload les images, crée le produit, déduit un jeton
     *
     * @param Client $client
     * @param array $data
     * @param UploadedFile $imageMain
     * @param UploadedFile|null $image1
     * @param UploadedFile|null $image2
     * @return Produit
     * @throws Exception
     */
    public function createProductForClient(
        Client $client, 
        array $data, 
        UploadedFile $imageMain,
        ?UploadedFile $image1 = null,
        ?UploadedFile $image2 = null
    ): Produit {
        // Vérifier que le client a des jetons
        if (!$this->canClientCreateProduct($client)) {
            throw new Exception('Jetons insuffisants. Vous devez avoir au moins 1 jeton pour créer un produit.');
        }

        DB::beginTransaction();
        try {
            // Upload l'image principale sur Cloudinary
            $uploadResultMain = $this->uploadProductImage($imageMain, 'client', (string)$client->id_client, 'main');
            
            $imageUrl1 = null;
            $imageUrl2 = null;

            // Upload image 1 si fournie
            if ($image1) {
                $uploadResult1 = $this->uploadProductImage($image1, 'client', (string)$client->id_client, '1');
                $imageUrl1 = $uploadResult1['url'];
            }

            // Upload image 2 si fournie
            if ($image2) {
                $uploadResult2 = $this->uploadProductImage($image2, 'client', (string)$client->id_client, '2');
                $imageUrl2 = $uploadResult2['url'];
            }

            // Créer le produit avec état "En attente" (id_state = 1)
            $produit = Produit::create([
                'nom_produits' => $data['nom_produits'],
                'prix_produits' => $data['prix_produits'],
                'description_produits' => $data['description_produits'],
                'image_produits' => $uploadResultMain['url'],
                'image_produits1' => $imageUrl1,
                'image_produits2' => $imageUrl2,
                'id_categorie' => $data['id_categorie'],
                'id_client' => $client->id_client,
                'id_user' => null, // Pas encore validé par un admin
                'id_state' => 1, // En attente
            ]);

            // Déduire 1 jeton normal du client
            $client->decrement('client_jettons');
            $client->refresh();

            DB::commit();

            // Envoyer email à tous les administrateurs
            $this->notifyAdminsNewProduct($produit, $client);

            Log::info("Produit créé par client", [
                'produit_id' => $produit->id_produits,
                'client_id' => $client->id_client,
                'jetons_restants' => $client->client_jettons
            ]);

            return $produit;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur création produit client: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mettre à jour un produit
     * Remplace les images si de nouvelles sont fournies
     *
     * @param Produit $produit
     * @param array $data
     * @param UploadedFile|null $imageMain
     * @param UploadedFile|null $image1
     * @param UploadedFile|null $image2
     * @return Produit
     * @throws Exception
     */
    public function updateProduct(
        Produit $produit, 
        array $data, 
        ?UploadedFile $imageMain = null,
        ?UploadedFile $image1 = null,
        ?UploadedFile $image2 = null
    ): Produit {
        // Vérifier si le produit peut être modifié
        if (!$this->canBeModified($produit)) {
            throw new Exception('Ce produit a déjà été validé et ne peut plus être modifié.');
        }

        DB::beginTransaction();
        try {
            // Si une nouvelle image principale est fournie
            if ($imageMain) {
                // Supprimer l'ancienne image de Cloudinary
                $publicId = $this->cloudinaryService->extractPublicId($produit->image_produits);
                if ($publicId) {
                    $this->cloudinaryService->deleteImage($publicId);
                }

                // Upload la nouvelle image
                $uploadResult = $this->uploadProductImage(
                    $imageMain, 
                    $produit->id_client ? 'client' : 'admin', 
                    (string)($produit->id_client ?? $produit->id_user),
                    'main'
                );
                $data['image_produits'] = $uploadResult['url'];
            }

            // Si une nouvelle image 1 est fournie
            if ($image1) {
                if ($produit->image_produits1) {
                    $publicId = $this->cloudinaryService->extractPublicId($produit->image_produits1);
                    if ($publicId) {
                        $this->cloudinaryService->deleteImage($publicId);
                    }
                }

                $uploadResult = $this->uploadProductImage(
                    $image1, 
                    $produit->id_client ? 'client' : 'admin', 
                    (string)($produit->id_client ?? $produit->id_user),
                    '1'
                );
                $data['image_produits1'] = $uploadResult['url'];
            }

            // Si une nouvelle image 2 est fournie
            if ($image2) {
                if ($produit->image_produits2) {
                    $publicId = $this->cloudinaryService->extractPublicId($produit->image_produits2);
                    if ($publicId) {
                        $this->cloudinaryService->deleteImage($publicId);
                    }
                }

                $uploadResult = $this->uploadProductImage(
                    $image2, 
                    $produit->id_client ? 'client' : 'admin', 
                    (string)($produit->id_client ?? $produit->id_user),
                    '2'
                );
                $data['image_produits2'] = $uploadResult['url'];
            }

            // Mettre à jour les autres champs
            $produit->update($data);

            DB::commit();

            Log::info("Produit mis à jour", ['produit_id' => $produit->id_produits]);

            return $produit;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur mise à jour produit: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Supprimer un produit et ses images
     *
     * @param Produit $produit
     * @return bool
     * @throws Exception
     */
    public function deleteProduct(Produit $produit): bool
    {
        DB::beginTransaction();
        try {
            // Supprimer les images de Cloudinary
            $publicId = $this->cloudinaryService->extractPublicId($produit->image_produits);
            if ($publicId) {
                $this->cloudinaryService->deleteImage($publicId);
            }

            if ($produit->image_produits1) {
                $publicId1 = $this->cloudinaryService->extractPublicId($produit->image_produits1);
                if ($publicId1) {
                    $this->cloudinaryService->deleteImage($publicId1);
                }
            }

            if ($produit->image_produits2) {
                $publicId2 = $this->cloudinaryService->extractPublicId($produit->image_produits2);
                if ($publicId2) {
                    $this->cloudinaryService->deleteImage($publicId2);
                }
            }

            $produit->delete();

            DB::commit();

            Log::info("Produit supprimé", ['produit_id' => $produit->id_produits]);

            return true;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur suppression produit: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Valider un produit par un administrateur
     *
     * @param Produit $produit
     * @param User $admin
     * @param string|null $comment Commentaire optionnel de l'admin
     * @return Produit
     * @throws Exception
     */
    public function validateProduct(Produit $produit, User $admin, ?string $comment = null): Produit
    {
        DB::beginTransaction();
        try {
            $produit->update([
                'id_state' => 2, // Validé
                'id_user' => $admin->id_user,
            ]);

            DB::commit();

            // Envoyer email au client SEULEMENT si un commentaire est fourni
            if ($comment && $produit->client && $produit->client->client_email) {
                $this->notifyClientProductValidated($produit, $comment);
            }

            Log::info("Produit validé", [
                'produit_id' => $produit->id_produits,
                'admin_id' => $admin->id_user,
                'has_comment' => !empty($comment)
            ]);

            // Envoyer la newsletter aux abonnés (en asynchrone)
            SendNewsletterJob::dispatch('product', $produit->load('client', 'categorie'));

            return $produit;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur validation produit: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Rejeter un produit par un administrateur
     * Recrédite 1 jeton au client
     *
     * @param Produit $produit
     * @param User $admin
     * @param string $reason Raison obligatoire du rejet
     * @return Produit
     * @throws Exception
     */
    public function rejectProduct(Produit $produit, User $admin, string $reason): Produit
    {
        if (empty(trim($reason))) {
            throw new Exception('La raison du rejet est obligatoire.');
        }

        DB::beginTransaction();
        try {
            $produit->update([
                'id_state' => 3, // Rejeté
                'id_user' => $admin->id_user,
            ]);

            // Recréditer 1 jeton au client s'il y en a un
            if ($produit->client) {
                $produit->client->increment('client_jettons');
            }

            DB::commit();

            // Envoyer email au client TOUJOURS avec la raison
            if ($produit->client && $produit->client->client_email) {
                $this->notifyClientProductRejected($produit, $reason);
            }

            Log::info("Produit rejeté et jeton recredité", [
                'produit_id' => $produit->id_produits,
                'admin_id' => $admin->id_user,
                'client_id' => $produit->id_client,
                'reason' => $reason
            ]);

            return $produit;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur rejet produit: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Créer un produit par un administrateur
     * Pas de vérification de jetons, pas de notification client
     *
     * @param User $admin
     * @param array $data
     * @param UploadedFile|null $imageMain
     * @param UploadedFile|null $image1
     * @param UploadedFile|null $image2
     * @return Produit
     * @throws Exception
     */
    public function createProductForAdmin(
        User $admin,
        array $data,
        ?UploadedFile $imageMain = null,
        ?UploadedFile $image1 = null,
        ?UploadedFile $image2 = null
    ): Produit {
        DB::beginTransaction();
        try {
            if ($imageMain) {
                $uploadResult = $this->uploadProductImage($imageMain, 'admin', (string)$admin->id_user, 'main');
                $data['image_produits'] = $uploadResult['url'];
            }

            if ($image1) {
                $uploadResult = $this->uploadProductImage($image1, 'admin', (string)$admin->id_user, '1');
                $data['image_produits1'] = $uploadResult['url'];
            }

            if ($image2) {
                $uploadResult = $this->uploadProductImage($image2, 'admin', (string)$admin->id_user, '2');
                $data['image_produits2'] = $uploadResult['url'];
            }

            $data['id_user'] = $admin->id_user;

            $produit = Produit::create($data);

            DB::commit();

            Log::info("Produit créé par admin", [
                'produit_id' => $produit->id_produits,
                'admin_id' => $admin->id_user,
            ]);

            return $produit;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur création produit admin: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Vérifier si un client peut créer un produit
     *
     * @param Client $client
     * @return bool
     */
    public function canClientCreateProduct(Client $client): bool
    {
        return $client->client_jettons > 0;
    }

    /**
     * Vérifier si un produit peut être modifié
     * Seulement si pas encore validé (id_state !== 2)
     *
     * @param Produit $produit
     * @return bool
     */
    public function canBeModified(Produit $produit): bool
    {
        return $produit->id_state !== 2;
    }

    /**
     * Upload une image de produit sur Cloudinary
     *
     * @param UploadedFile $image
     * @param string $creatorType ('client' ou 'admin')
     * @param string $creatorId
     * @param string $imageType ('main', '1', ou '2')
     * @return array
     */
    protected function uploadProductImage(
        UploadedFile $image, 
        string $creatorType, 
        string $creatorId,
        string $imageType = 'main'
    ): array {
        // Dossier sur Cloudinary : products/{creator_type}/{creator_id}/{image_type}
        $folder = "products/{$creatorType}/{$creatorId}";
        
        return $this->cloudinaryService->uploadImage($image, $folder, [
            'transformation' => [
                'width' => 800,
                'height' => 800,
                'crop' => 'limit',
                'quality' => 'auto',
                'fetch_format' => 'auto'
            ]
        ]);
    }

    /**
     * Notifier les admins qu'un nouveau produit est en attente
     *
     * @param Produit $produit
     * @param Client $client
     * @return void
     */
    protected function notifyAdminsNewProduct(Produit $produit, Client $client): void
    {
        try {
            $admins = User::all();

            foreach ($admins as $admin) {
                if ($admin->user_email) {
                    Mail::to($admin->user_email)->send(new ProductPendingValidation($produit, $client));
                }
            }
        } catch (Exception $e) {
            Log::error('Erreur envoi email admins nouveau produit: ' . $e->getMessage());
        }
    }

    /**
     * Notifier le client que son produit a été validé
     *
     * @param Produit $produit
     * @param string $comment Commentaire optionnel de l'admin
     * @return void
     */
    protected function notifyClientProductValidated(Produit $produit, string $comment): void
    {
        try {
            Mail::to($produit->client->client_email)->send(new ProductValidated($produit, $comment));
        } catch (Exception $e) {
            Log::error('Erreur envoi email validation produit: ' . $e->getMessage());
        }
    }

    /**
     * Notifier le client que son produit a été rejeté
     *
     * @param Produit $produit
     * @param string $reason Raison obligatoire du rejet
     * @return void
     */
    protected function notifyClientProductRejected(Produit $produit, string $reason): void
    {
        try {
            Mail::to($produit->client->client_email)->send(new ProductRejected($produit, $reason));
        } catch (Exception $e) {
            Log::error('Erreur envoi email rejet produit: ' . $e->getMessage());
        }
    }
}
