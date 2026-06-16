<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vue extends Model
{
    protected $table = 'vues';
    protected $primaryKey = 'id_vues';
    public $timestamps = false;

    protected $fillable = [
        'IP',
        'id_produits',
    ];

    // Relations
    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class, 'id_produits', 'id_produits');
    }
}
