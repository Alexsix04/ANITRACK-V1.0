<?php

// app/Models/AnimeFavorite.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnimeFavorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'anime_id',
        'anime_title',
        'anime_image',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
