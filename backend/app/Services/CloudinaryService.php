<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Api\Upload\UploadApi;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Exception;

class CloudinaryService
{
    protected ?Cloudinary $cloudinary = null;
    protected array $config;

    public function __construct()
    {
        $this->config = config('cloudinary');
        
        if (empty($this->config['cloud_name']) || empty($this->config['api_key']) || empty($this->config['api_secret'])) {
            $this->cloudinary = null;
            return;
        }

        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => $this->config['cloud_name'],
                'api_key' => $this->config['api_key'],
                'api_secret' => $this->config['api_secret'],
            ],
            'url' => [
                'secure' => $this->config['secure']
            ],
            'api' => [
                'verify' => false,
                'upload_timeout' => 0,
            ],
        ]);
    }

    /**
     * Upload une image vers Cloudinary
     *
     * @param UploadedFile $file
     * @param string $folder
     * @param array $options
     * @return array
     * @throws Exception
     */
    public function uploadImage(UploadedFile $file, string $folder = 'avatars', array $options = []): array
    {
        if ($this->cloudinary === null) {
            Log::warning('Cloudinary non configuré — upload ignoré');
            return ['success' => false, 'message' => 'Cloudinary non configuré'];
        }

        try {
            $defaultOptions = [
                'folder' => $this->config['folder'] . '/' . $folder,
                'resource_type' => 'image',
                'overwrite' => true,
                'invalidate' => true,
            ];

            $uploadOptions = array_merge($defaultOptions, $options);

            $result = $this->cloudinary->uploadApi()->upload(
                $file->getRealPath(),
                $uploadOptions
            );

            return [
                'success' => true,
                'url' => $result['secure_url'],
                'public_id' => $result['public_id'],
                'width' => $result['width'],
                'height' => $result['height'],
                'format' => $result['format'],
            ];

        } catch (Exception $e) {
            Log::error('Cloudinary upload error: ' . $e->getMessage());
            throw new Exception('Erreur lors de l\'upload de l\'image: ' . $e->getMessage());
        }
    }

    /**
     * Upload un avatar avec transformation
     *
     * @param UploadedFile $file
     * @param string $userId
     * @return array
     * @throws Exception
     */
    public function uploadAvatar(UploadedFile $file, string $userId): array
    {
        $transformation = $this->config['avatar_transformation'];
        
        return $this->uploadImage($file, 'avatars', [
            'public_id' => 'avatar_' . $userId,
            'transformation' => [
                [
                    'width' => $transformation['width'],
                    'height' => $transformation['height'],
                    'crop' => $transformation['crop'],
                    'gravity' => $transformation['gravity'],
                ],
                [
                    'quality' => $transformation['quality'],
                    'fetch_format' => $transformation['fetch_format'],
                ]
            ]
        ]);
    }

    /**
     * Upload une image de catégorie avec transformation
     *
     * @param UploadedFile $file
     * @return array
     * @throws Exception
     */
    public function uploadCategoryImage(UploadedFile $file): array
    {
        return $this->uploadImage($file, 'categories', [
            'transformation' => [
                [
                    'width' => 400,
                    'height' => 300,
                    'crop' => 'fill',
                    'gravity' => 'center',
                ],
                [
                    'quality' => 'auto',
                    'fetch_format' => 'auto',
                ]
            ]
        ]);
    }

    /**
     * Supprimer une image de Cloudinary
     *
     * @param string $publicId
     * @return bool
     */
    public function deleteImage(string $publicId): bool
    {
        if ($this->cloudinary === null) {
            Log::warning('Cloudinary non configuré — suppression ignorée');
            return false;
        }

        try {
            $result = $this->cloudinary->uploadApi()->destroy($publicId);
            return $result['result'] === 'ok';
        } catch (Exception $e) {
            Log::error('Cloudinary delete error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Extraire le public_id d'une URL Cloudinary
     *
     * @param string $url
     * @return string|null
     */
    public function extractPublicId(string $url): ?string
    {
        if (strpos($url, 'cloudinary.com') === false) {
            return null;
        }

        // Extraire le public_id de l'URL
        preg_match('/\/upload\/(?:v\d+\/)?(.+)\.[a-z]+$/', $url, $matches);
        return $matches[1] ?? null;
    }
}
