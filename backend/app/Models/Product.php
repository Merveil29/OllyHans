<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'product_line_id', 'category_id', 'name', 'slug', 'description',
        'bienfaits', 'composition', 'conseils_utilisation', 'skin_type',
        'effet', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relations
    public function productLine(): BelongsTo
    {
        return $this->belongsTo(ProductLine::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function retailVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->where('type', 'retail')->where('is_active', true);
    }

    public function wholesaleVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->where('type', 'wholesale')->where('is_active', true);
    }
}
