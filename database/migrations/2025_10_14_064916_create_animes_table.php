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
        Schema::create('animes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('anilist_id')->unique()->index(); // ID de Anilist
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('format')->nullable();
            $table->integer('episodes')->nullable();
            $table->string('season')->nullable();
            $table->integer('season_year')->nullable();
            $table->string('status')->nullable();
            $table->integer('average_score')->nullable();
            $table->json('genres')->nullable();
            $table->json('studios')->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anime');
    }
};
