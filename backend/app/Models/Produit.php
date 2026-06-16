<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produit extends Model
{
    protected $table = 'produits';
    protected $primaryKey = 'id_produits';
    public $timestamps = false;

    protected $fillable = [
        'nom_produits',
        'prix_produits',
        'description_produits',
        'image_produits',
        'image_produits1',
        'image_produits2',
        'id_categorie',
        'id_client',
        'id_user',
        'id_state',
        'dateSaisie',
    ];

    // Relations
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'id_client', 'id_client');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class, 'id_categorie', 'id_categorie');
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'id_state', 'id_state');
    }

    public function vues(): HasMany
    {
        return $this->hasMany(Vue::class, 'id_produits', 'id_produits');
    }
}
