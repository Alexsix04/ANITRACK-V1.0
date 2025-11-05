<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anime_favorites', function (Blueprint $table) {
            $table->id();
            
            // Usuario que guarda el favorito
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');

            // Referencia al anime local
            $table->foreignId('anime_id')
                  ->constrained('animes')  // referencia a la tabla animes
                  ->onDelete('cascade');
            // Referencia al anime en Anilist
            $table->integer('anilist_id');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anime_favorites');
    }
};
