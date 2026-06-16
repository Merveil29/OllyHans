<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id_client');
            $table->string('client_nom', 150);
            $table->string('client_prenom', 30);
            $table->string('client_email', 250);
            $table->string('client_login', 250);
            $table->string('client_adresse', 250);
            $table->string('client_tel', 250);
            $table->string('client_password', 250);
            $table->string('client_activation_code', 250)->nullable();
            $table->enum('client_email_status', ['non vérifier', 'vérifier']);
            $table->integer('client_otp')->default(0);
            $table->integer('etape')->default(1);
            $table->string('image_client', 255)->nullable();
            $table->integer('client_jettons')->nullable();
            $table->integer('client_jettons_sponsor')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
