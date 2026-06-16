<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class Client extends Model
{
    use HasApiTokens;

    protected $table = 'clients';
    protected $primaryKey = 'id_client';
    public $timestamps = false;

    protected $fillable = [
        'client_nom',
        'client_prenom',
        'client_email',
        'client_login',
        'client_tel',
        'client_adresse',
        'client_password',
        'image_client',
        'client_activation_code',
        'client_email_status',
        'client_otp',
        'etape',
        'client_jettons',
        'client_jettons_sponsor',
    ];

    protected $hidden = [
        'client_password',
    ];

    // Relations
    public function produits(): HasMany
    {
        return $this->hasMany(Produit::class, 'id_client', 'id_client');
    }
}
