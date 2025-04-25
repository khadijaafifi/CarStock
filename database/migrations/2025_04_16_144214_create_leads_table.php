<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();

            // Champs nécessaires pour compatibilité avec le controller
            $table->string('session_id')->nullable(); // Permet d'enregistrer plusieurs leads par session
            $table->string('name')->nullable();       // Peut être null si pas encore fourni
            $table->string('email')->nullable();      // Peut être null si pas encore fourni
            $table->string('phone')->nullable();      // Pour numéro de téléphone
            $table->text('summary')->nullable();      // Résumé de la discussion

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('leads');
    }
};
