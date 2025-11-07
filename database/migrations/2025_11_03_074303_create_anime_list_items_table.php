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
        Schema::create('anime_list_items', function (Blueprint $table) {
            $table->id();

            // Relación con la lista del usuario
            $table->foreignId('anime_list_id')->constrained()->cascadeOnDelete();

            // Relación con el anime local
            $table->foreignId('anime_id')->constrained('animes')->cascadeOnDelete();

            // ID original de AniList (para poder seguir mostrando/enlazando desde la API)
            $table->unsignedBigInteger('anilist_id');

            // Datos del seguimiento
            $table->unsignedInteger('episode_progress')->default(0);
            $table->unsignedTinyInteger('score')->nullable();
            $table->enum('status', ['watching', 'completed', 'on_hold', 'dropped', 'plan_to_watch'])
                ->default('plan_to_watch');
            $table->text('notes')->nullable();
            $table->boolean('is_rewatch')->default(false);
            $table->unsignedInteger('rewatch_count')->default(0);

            // Evitar duplicados del mismo anime en la misma lista
            $table->unique(['anime_list_id', 'anime_id']);

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anime_list_items');
    }
};
