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
            'banner' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ]);

        // Actualizar bio
        $user->bio = $request->bio;

        // ===================================================
        // ACTUALIZAR AVATAR
        // ===================================================
        if ($request->hasFile('avatar')) {

            // Si ya tenía un avatar anterior y no es el por defecto
            if ($user->avatar && !str_contains($user->avatar, 'avatar-default.png')) {
                $avatarPath = str_replace(asset('storage/') . '/', '', $user->avatar);
                \Storage::disk('public')->delete($avatarPath);
            }

            // Guardar el nuevo avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        // ===================================================
        // ACTUALIZAR BANNER
        // ===================================================
        if ($request->hasFile('banner')) {

            // Si ya tenía un banner anterior, eliminarlo
            if ($user->banner) {
                $bannerPath = str_replace(asset('storage/') . '/', '', $user->banner);
                \Storage::disk('public')->delete($bannerPath);
            }

            // Guardar nuevo banner
            $bannerPath = $request->file('banner')->store('banners', 'public');
            $user->banner = $bannerPath;
        }

        // Guardar cambios
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
