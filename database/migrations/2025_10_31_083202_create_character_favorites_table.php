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
        Schema::create('character_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Datos básicos del personaje
            $table->unsignedBigInteger('character_id'); // ID del personaje en la API
            $table->string('character_name');
            $table->string('character_image')->nullable();

            $table->timestamps();

            // Evitar duplicados
            $table->unique(['user_id', 'character_id']);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('character_favorites');
    }
};
