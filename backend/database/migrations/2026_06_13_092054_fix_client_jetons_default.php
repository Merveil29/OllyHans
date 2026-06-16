<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('UPDATE clients SET client_jettons = 0 WHERE client_jettons IS NULL');
        Schema::table('clients', function (Blueprint $table) {
            $table->integer('client_jettons')->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->integer('client_jettons')->nullable(false)->change();
        });
    }
};
