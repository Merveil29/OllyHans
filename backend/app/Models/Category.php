<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'name', 'slug', 'description', 'image', 'icon', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relations
    public function productLines(): HasMany
    {
        return $this->hasMany(ProductLine::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
