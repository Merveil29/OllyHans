<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('state', function (Blueprint $table) {
            $table->tinyIncrements('id_state');
            $table->string('title', 100);
            $table->string('autre', 100)->nullable();
        });

        DB::table('state')->insert([
            ['id_state' => 1, 'title' => 'En attente', 'autre' => null],
            ['id_state' => 2, 'title' => 'Publié', 'autre' => null],
            ['id_state' => 3, 'title' => 'Rejeté', 'autre' => null],
            ['id_state' => 4, 'title' => 'Premium', 'autre' => null],
            ['id_state' => 5, 'title' => 'Désactivé', 'autre' => null],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('state');
    }
};
