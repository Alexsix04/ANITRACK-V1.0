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
        Schema::create('character_list_items', function (Blueprint $table) {
            $table->id();

            // === Relaciones principales ===
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('list_id')->constrained('character_lists')->onDelete('cascade');

            // === Relaciones con datos locales ===
            $table->foreignId('character_id')->constrained('characters')->onDelete('cascade');
            $table->foreignId('anime_id')->constrained('animes')->onDelete('cascade');

            // === IDs externos (Anilist) ===
            $table->unsignedBigInteger('anime_anilist_id')->index();
            $table->unsignedBigInteger('anilist_id')->index()->comment('ID del personaje en Anilist');

            // === Información adicional del personaje en la lista ===
            $table->unsignedTinyInteger('score')->nullable()->comment('Puntuación del 1 al 10');
            $table->text('notes')->nullable();

            $table->timestamps();

            // === Restricciones ===
            $table->unique(['user_id', 'list_id', 'character_id'], 'unique_character_in_list');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('character_list_items');
    }
};

