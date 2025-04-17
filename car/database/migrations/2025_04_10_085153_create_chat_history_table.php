<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('chat_history', function (Blueprint $table) {
            $table->id();
            $table->string('session_id'); // Pour lier les conversations à un utilisateur/session
            $table->text('lead_message');
            $table->text('assistant_response');
            $table->text('meta_data');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_history');
    }
};
