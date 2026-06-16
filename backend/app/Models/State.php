<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    protected $table = 'state';
    protected $primaryKey = 'id_state';
    public $timestamps = false;

    protected $fillable = [
        'title',
        'autre',
    ];

    // Relations
    public function produits(): HasMany
    {
        return $this->hasMany(Produit::class, 'id_state', 'id_state');
    }
}
