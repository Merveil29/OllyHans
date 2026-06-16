<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsletterSubscriber extends Model
{
    protected $table = 'newsletter_subscribers';

    protected $fillable = [
        'email',
        'nom',
        'unsubscribe_token',
        'is_active',
        'notify_products',
        'notify_blog',
        'subscribed_at',
        'unsubscribed_at',
    ];

    protected $casts = [
        'is_active'       => 'boolean',
        'notify_products' => 'boolean',
        'notify_blog'     => 'boolean',
        'subscribed_at'   => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    /**
     * Génère un token unique de désinscription avant la création.
     */
    protected static function booted(): void
    {
        static::creating(function (self $subscriber) {
            if (empty($subscriber->unsubscribe_token)) {
                $subscriber->unsubscribe_token = Str::random(64);
            }
        });
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWantsProducts($query)
    {
        return $query->active()->where('notify_products', true);
    }

    public function scopeWantsBlog($query)
    {
        return $query->active()->where('notify_blog', true);
    }
}
