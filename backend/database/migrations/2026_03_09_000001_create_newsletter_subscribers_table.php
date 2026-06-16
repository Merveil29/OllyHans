<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('nom')->nullable();
            $table->string('unsubscribe_token', 64)->unique();
            $table->boolean('is_active')->default(true);
            $table->boolean('notify_products')->default(true);   // Recevoir les notifs de nouveaux produits
            $table->boolean('notify_blog')->default(true);       // Recevoir les notifs de nouveaux blogs
            $table->timestamp('subscribed_at')->useCurrent();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamps();

            $table->index('is_active');
            $table->index(['is_active', 'notify_products']);
            $table->index(['is_active', 'notify_blog']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscribers');
    }
};
