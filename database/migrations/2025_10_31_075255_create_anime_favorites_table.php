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
        Schema::create('anime_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('user_name')->nullable();

            // Identificador del anime en la API
            $table->unsignedBigInteger('anime_id');
            $table->string('anime_title');
            $table->string('anime_image')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'anime_id']);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anime_favorites');
    }
};
