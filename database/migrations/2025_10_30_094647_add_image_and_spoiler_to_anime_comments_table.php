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
        Schema::table('anime_comments', function (Blueprint $table) {
            $table->string('image')->nullable()->after('content'); // imagen opcional
            $table->boolean('is_spoiler')->default(false)->after('image'); // spoiler
        });
    }

    public function down()
    {
        Schema::table('anime_comments', function (Blueprint $table) {
            $table->dropColumn(['image', 'is_spoiler']);
        });
    }
};
