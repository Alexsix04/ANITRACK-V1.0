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
            $table->unsignedBigInteger('anilist_id')->unique(); // ID de Anilist
            $table->string('name');
            $table->string('native_name')->nullable();
            $table->text('description')->nullable();
            $table->string('role')->nullable();
            $table->string('gender')->nullable();
            $table->string('age')->nullable(); // cambiado de date() → string()
            $table->string('image_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('characters');
    }
};