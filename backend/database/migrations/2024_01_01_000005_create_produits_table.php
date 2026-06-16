<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('produits', function (Blueprint $table) {
            $table->increments('id_produits');
            $table->string('nom_produits', 255);
            $table->string('prix_produits', 10);
            $table->string('description_produits', 500);
            $table->string('image_produits', 255);
            $table->string('image_produits1', 255)->nullable();
            $table->string('image_produits2', 255)->nullable();
            $table->integer('id_sous_categorie');
            $table->integer('id_client')->nullable();
            $table->integer('id_user')->nullable();
            $table->integer('id_state')->nullable();
            $table->dateTime('dateSaisie')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produits');
    }
};
