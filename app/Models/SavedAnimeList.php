<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedAnimeList extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'anime_list_id',
        'owner_id',
        'owner_name',
    ];

    public function list()
    {
        return $this->belongsTo(AnimeList::class, 'anime_list_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}