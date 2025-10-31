<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\AnimeComment;
use App\Models\AnimeFavorite;
use App\Models\CharacterFavorite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function index()
    {
        $user = auth()->user();

        //Animes Favoritos
        $animeFavorites = AnimeFavorite::where('user_id', $user->id)->get();
        $characterFavorites = CharacterFavorite::where('user_id', $user->id)->get();

        return view('profile.index', compact('user', 'animeFavorites', 'characterFavorites'));
    }

    public function updateBioAvatar(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Actualizar bio
        $user->bio = $request->bio;

        // Si el usuario sube una nueva imagen
        if ($request->hasFile('avatar')) {

            // Si ya tenía un avatar anterior (y no es el por defecto), eliminarlo del storage
            if ($user->avatar && !str_contains($user->avatar, 'avatar-default.png')) {
                // Eliminamos del disco 'public' (usa la ruta relativa, no la URL)
                $avatarPath = $user->avatar;

                // Si en BD está guardada la URL completa, quitamos el dominio
                $avatarPath = str_replace(asset('storage/') . '/', '', $avatarPath);

                \Storage::disk('public')->delete($avatarPath);
            }

            // Guardar el nuevo avatar en 'storage/app/public/avatars'
            $path = $request->file('avatar')->store('avatars', 'public');

            // Guardamos solo la ruta relativa, NO la URL completa
            $user->avatar = $path;
        }

        $user->save();

        return back()->with('success', 'Perfil actualizado correctamente.');
    }


    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
