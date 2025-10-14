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
        Schema::create('character', function (Blueprint $table) {
            $table->id();
            $table->integer('anilist_id')->unique(); // ID de Anilist
            $table->string('name');
            $table->string('native_name')->nullable();
            $table->text('description')->nullable();
            $table->string('role')->nullable();
            $table->string('gender')->nullable();
            $table->date('age')->nullable();
            $table->string('image_url')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('character');
    }
};
