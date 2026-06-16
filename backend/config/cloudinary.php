<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cloudinary Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration pour Cloudinary upload service
    | 
    */

    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
    'api_key' => env('CLOUDINARY_API_KEY'),
    'api_secret' => env('CLOUDINARY_API_SECRET'),
    'secure' => env('CLOUDINARY_SECURE', true),
    'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET', null),
    
    // Dossier par défaut pour les uploads
    'folder' => env('CLOUDINARY_FOLDER', 'topideal'),
    
    // Transformations par défaut pour les avatars
    'avatar_transformation' => [
        'width' => 400,
        'height' => 400,
        'crop' => 'fill',
        'gravity' => 'face',
        'quality' => 'auto',
        'fetch_format' => 'auto',
    ],
];
