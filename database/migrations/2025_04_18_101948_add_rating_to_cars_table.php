<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

public function up() {
    Schema::table('cars', function (Blueprint $table) {
        $table->unsignedTinyInteger('rating')  // Stocke des entiers 0-255
              ->default(0)                    // Valeur par défaut = 0
              ->after('modele');               // Placement optionnel
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            //
        });
    }
};
