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
        Schema::create('anime_comment_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anime_comment_id')
                ->constrained('anime_comments')
                ->cascadeOnDelete();

            // user_id debe coincidir con users.id (BIGINT UNSIGNED)
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('user_name')->nullable();
            $table->timestamps();

            $table->unique(['anime_comment_id', 'user_id'], 'anime_comment_user_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anime_comment_likes');
    }
};
