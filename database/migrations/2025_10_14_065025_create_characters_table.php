<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('characters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('anime_id'); // ID de Anime en la base de datos local
            $table->unsignedBigInteger('anime_anilist_id'); // ID de Anime en Anilist
            $table->unsignedBigInteger('anilist_id')->unique(); // ID del personaje en Anilist
            $table->string('name');
            $table->string('native_name')->nullable();
            $table->text('description')->nullable();
            $table->string('role')->nullable();
            $table->string('gender')->nullable();
            $table->string('age')->nullable(); 
            $table->string('image_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('characters');
    }
};