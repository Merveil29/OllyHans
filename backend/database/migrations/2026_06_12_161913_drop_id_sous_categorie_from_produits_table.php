<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produits', function (Blueprint $table) {
            $table->dropForeign(['id_sous_categorie']);
            $table->dropColumn('id_sous_categorie');
        });
    }

    public function down(): void
    {
        Schema::table('produits', function (Blueprint $table) {
            $table->unsignedInteger('id_sous_categorie')->nullable()->after('id_categorie');
        });
    }
};
