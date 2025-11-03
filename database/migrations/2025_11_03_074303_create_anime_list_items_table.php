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
        Schema::create('anime_list_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anime_list_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('anime_id');
            $table->string('anime_title');
            $table->string('anime_image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anime_list_items');
    }
};
