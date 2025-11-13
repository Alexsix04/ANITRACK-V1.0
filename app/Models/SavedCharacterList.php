<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedCharacterList extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'character_list_id',
        'owner_id',
        'owner_name',
    ];

    public function list()
    {
        return $this->belongsTo(CharacterList::class, 'character_list_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
