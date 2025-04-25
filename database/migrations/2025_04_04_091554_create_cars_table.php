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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('marque'); // Car brand
            $table->string('modele'); // Car model
            $table->integer('annee'); // Car year
         $table->string('couleur')->default('Non spécifié');
            $table->decimal('prix', 8, 2); // Car price (up to 8 digits, 2 decimals)
            $table->text('description'); // Car description
            $table->string('image')->nullable(); // Car image (nullable, in case no image is uploaded)
            $table->timestamps(); // Created at and updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
