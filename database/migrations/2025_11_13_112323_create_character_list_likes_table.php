<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('character_list_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('character_list_id')->constrained('character_lists')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'character_list_id']); // un usuario solo puede dar like una vez
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_list_likes');
    }
};
