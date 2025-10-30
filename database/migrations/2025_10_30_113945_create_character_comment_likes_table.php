<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('character_comment_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('character_comment_id')
                ->constrained('character_comments')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('user_name')->nullable();
            $table->timestamps();

            $table->unique(['character_comment_id', 'user_id'], 'character_comment_user_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_comment_likes');
    }
};