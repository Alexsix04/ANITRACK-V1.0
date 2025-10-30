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
        Schema::create('character_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('character_id'); // sin foreign key, como anime_comments
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->text('content');
            $table->unsignedBigInteger('likes_count')->default(0);
            $table->string('image')->nullable(); // imagen opcional
            $table->boolean('is_spoiler')->default(false); // spoiler opcional
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('character_comments');
    }
};