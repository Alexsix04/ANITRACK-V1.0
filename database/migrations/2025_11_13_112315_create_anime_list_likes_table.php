<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('anime_list_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('anime_list_id')->constrained('anime_lists')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'anime_list_id']); // un usuario solo puede dar like una vez
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anime_list_likes');
    }
};
