<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnimeCommentLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'anime_comment_id',
        'user_id',
        'user_name',
    ];

    public function comment()
    {
        return $this->belongsTo(AnimeComment::class, 'anime_comment_id');
    }
}
