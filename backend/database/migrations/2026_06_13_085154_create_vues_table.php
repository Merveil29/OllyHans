<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vues', function (Blueprint $table) {
            $table->increments('id_vues');
            $table->string('IP', 45);
            $table->unsignedInteger('id_produits');
            $table->dateTime('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vues');
    }
};
