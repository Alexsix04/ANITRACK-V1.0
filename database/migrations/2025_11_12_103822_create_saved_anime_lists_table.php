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
        Schema::create('saved_anime_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // usuario que guarda
            $table->foreignId('anime_list_id')->constrained()->onDelete('cascade'); // lista guardada
            $table->unsignedBigInteger('owner_id'); // usuario propietario de la lista
            $table->string('owner_name'); // nombre del usuario propietario
            $table->boolean('is_public')->default(false); // visibilidad de la lista guardada
            $table->timestamps();

            $table->unique(['user_id', 'anime_list_id']); // evita duplicados
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saved_anime_lists');
    }
};
