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
    Schema::table('reviews', function (Blueprint $table) {
        $table->string('session_id')->nullable()->after('rating');
        $table->unique(['car_id', 'session_id']);
    });
}

public function down()
{
    Schema::table('reviews', function (Blueprint $table) {
        $table->dropUnique(['car_id', 'session_id']);
        $table->dropColumn('session_id');
    });
}
    /**
     * Reverse the migrations.
     */

};
