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
        Schema::create('anime_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('anime_id');
            $table->string('user_name')->nullable();
            $table->text('content');
            $table->unsignedBigInteger('likes_count')->default(0);
            $table->timestamps();
        });
    }
 
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anime_comments');
    }
};
