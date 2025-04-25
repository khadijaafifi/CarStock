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
        Schema::table('reviews', function (Blueprint $table) {
            // Supprimer l'unique existant
            $table->dropUnique(['car_id', 'session_id']);

            
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Revenir à l'ancien index unique
            $table->unique(['car_id', 'session_id']);
        });
    }
};


