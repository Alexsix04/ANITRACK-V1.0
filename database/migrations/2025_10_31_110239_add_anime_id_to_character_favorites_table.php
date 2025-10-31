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
        Schema::table('character_favorites', function (Blueprint $table) {
            $table->unsignedBigInteger('anime_id')->after('character_id');
        });
    }

    public function down()
    {
        Schema::table('character_favorites', function (Blueprint $table) {
            $table->dropColumn('anime_id');
        });
    }
};
