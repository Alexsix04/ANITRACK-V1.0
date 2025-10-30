<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CharacterCommentLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'character_comment_id',
        'user_id',
        'user_name',
    ];

    public function comment()
    {
        return $this->belongsTo(CharacterComment::class, 'character_comment_id');
    }
}