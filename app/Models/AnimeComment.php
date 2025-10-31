<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnimeComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'anime_id',
        'user_id',
        'user_name',
        'content',
        'likes_count',
        'image',
        'is_spoiler'
    ];
    
    // Relación al usuario
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación con likes
    public function likes()
    {
        return $this->hasMany(AnimeCommentLike::class);
    }
}