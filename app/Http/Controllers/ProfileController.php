<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\AnimeComment;
use App\Models\AnimeFavorite;
use App\Models\AnimeList;
use App\Models\User;
use App\Models\CharacterList;
use App\Models\CharacterFavorite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show($id)
    {
        $user = User::findOrFail($id);
        $viewer = auth()->user();
        $isOwner = $viewer && $viewer->id === $user->id;

        /* ==========================================================
        FAVORITOS
       ========================================================== */

        if ($isOwner) {
            $animeFavorites = $user->animeFavorites()->with('anime')->get();
            $characterFavorites = $user->characterFavorites()->with('character')->get();
        } else {
            // Solo favoritos públicos si decides hacerlos públicos (si no, quítalo)
            $animeFavorites = $user->animeFavorites()
                ->where('is_public', true)
                ->with('anime')
                ->get();

            $characterFavorites = $user->characterFavorites()
                ->where('is_public', true)
                ->with('character')
                ->get();
        }

        /* ==========================================================
        LISTAS DE ANIME
       ========================================================== */

        $animeLists = AnimeList::with([
            'items.anime',
            'savedByUsers' => fn($q) => $q->where('user_id', $viewer?->id ?? 0)
        ])
            ->where('user_id', $user->id)
            ->when(!$isOwner, fn($q) => $q->where('is_public', true))
            ->get();


        /* ==========================================================
        LISTAS DE PERSONAJES
       ========================================================== */

        $characterLists = CharacterList::with([
            'items.character',
            'savedByUsers' => fn($q) => $q->where('user_id', $viewer?->id ?? 0)
        ])
            ->where('user_id', $user->id)
            ->when(!$isOwner, fn($q) => $q->where('is_public', true))
            ->get();

        /* ==========================================================
        RETORNO
       ========================================================== */

        return view('profile.show', compact(
            'user',
            'viewer',
            'isOwner',
            'animeFavorites',
            'characterFavorites',
            'animeLists',
            'characterLists',
        ));
    }
    /**
     * Display the user's profile.
     */
    public function index()
    {
        $user = auth()->user();

        // ⚙️ Relación con los animes locales
        $animeFavorites = $user->animeFavorites()
            ->with('anime') // trae la info del anime local
            ->get();

        // ⚙️ Relación con los personajes (si la tienes)
        $characterFavorites = $user->characterFavorites()
            ->with('character')
            ->get();


        // Listas por defecto (Vistos y Pendientes)
        $defaultLists = AnimeList::with(['items'])
            ->where('user_id', $user->id)
            ->whereIn('name', ['Vistos', 'Pendientes'])
            ->get();
        // Todas las listas de animes del usuario
        $allLists = AnimeList::with('items')
            ->where('user_id', $user->id)
            ->get();


        //  Listas de personajes 
        $characterLists = \App\Models\CharacterList::with(['items.character'])
            ->where('user_id', $user->id)
            ->get();

        return view('profile.index', compact('user', 'animeFavorites', 'characterFavorites', 'defaultLists', 'allLists', 'characterLists'));
    }

    public function saves()
    {
        $user = auth()->user();

        // Listas de anime guardadas
        $savedAnimeLists = $user->savedAnimeLists()
            ->with(['items.anime', 'user']) // cargamos relaciones necesarias
            ->latest()
            ->get();

        // Listas de personajes guardadas
        $savedCharacterLists = $user->savedCharacterLists()
            ->with(['items.character', 'user']) // cargamos relaciones necesarias
            ->latest()
            ->get();

        return view('profile.saves', compact('user', 'savedAnimeLists', 'savedCharacterLists'));
    }



    public function updateBioAvatar(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'banner' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ]);

        // Subida de avatar
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath; //  SOLO guardamos "avatars/archivo.png"
        }

        // Subida de banner
        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('banners', 'public');
            $user->banner = $bannerPath;
        }

        // Actualizar bio
        $user->bio = $request->bio;
        $user->save();

        return redirect()->back()->with('success', 'Perfil actualizado correctamente.');
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
