<?php

namespace App\Observers;

use App\Models\User;
use App\Models\AnimeList;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user)
    {
        AnimeList::create(['user_id' => $user->id, 'name' => 'Vistos', 'is_public' => false]);
        AnimeList::create(['user_id' => $user->id, 'name' => 'Pendientes', 'is_public' => false]);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
