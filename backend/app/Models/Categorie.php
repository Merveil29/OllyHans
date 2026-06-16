<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categorie extends Model
{
    protected $table = 'categorie';
    protected $primaryKey = 'id_categorie';
    public $timestamps = false;

    protected $fillable = [
        'nom_categorie',
        'image_categorie',
    ];

    // Relations
    public function sousCategories(): HasMany
    {
        return $this->hasMany(SousCategorie::class, 'id_categorie', 'id_categorie');
    }

    public function produits()
    {
        return $this->hasMany(Produit::class, 'id_categorie', 'id_categorie');
    }
}
