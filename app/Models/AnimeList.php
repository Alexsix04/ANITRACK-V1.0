<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnimeList extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'is_public', 'description'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(AnimeListItem::class, 'anime_list_id');
    }
}
