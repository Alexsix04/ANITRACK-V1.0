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
        Schema::create('saved_character_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('character_list_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('owner_id');
            $table->string('owner_name');
            $table->boolean('is_public')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'character_list_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saved_character_lists');
    }
};
