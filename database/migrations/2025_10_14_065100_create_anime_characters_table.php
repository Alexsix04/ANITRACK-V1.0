<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anime_characters', function (Blueprint $table) {
            $table->id();

            $table->foreignId('anime_id')
                ->constrained('animes')
                ->onDelete('cascade');

            $table->foreignId('character_id')
                ->constrained('characters')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anime_characters');
    }
};
