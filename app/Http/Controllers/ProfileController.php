<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\AnimeFavorite;
use App\Models\AnimeList;
use App\Models\Anime;
use App\Models\User;
use App\Models\CharacterList;
use App\Models\CharacterListItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
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
       LISTAS DE ANIME Y PERSONAJES
    ========================================================== */
        $animeLists = AnimeList::with([
            'items.anime',
            'savedByUsers' => fn($q) => $q->where('user_id', $viewer?->id ?? 0)
        ])
            ->where('user_id', $user->id)
            ->when(!$isOwner, fn($q) => $q->where('is_public', true))
            ->get();

        $characterLists = CharacterList::with([
            'items.character',
            'savedByUsers' => fn($q) => $q->where('user_id', $viewer?->id ?? 0)
        ])
            ->where('user_id', $user->id)
            ->when(!$isOwner, fn($q) => $q->where('is_public', true))
            ->get();

        /* ==========================================================
   ESTADÍSTICAS "Vistos" - ANIMES
========================================================== */
        $vistosList = $user->animeLists()->where('name', 'Vistos')->first();

        $totalVistos = 0;
        $animeFavorito = null;
        $notaMedia = 0;
        $generosFavoritos = collect();

        if ($vistosList) {
            // Obtener todos los items de la lista con su anime
            $items = $vistosList->items()->with('anime')->get();
            $totalVistos = $items->count();

            if ($totalVistos > 0) {
                // 1️⃣ Anime favorito: mayor score, desempate por created_at
                $animeFavoritoItem = $vistosList->items()
                    ->with('anime')
                    ->orderByDesc('score')
                    ->orderBy('created_at')
                    ->first();
                $animeFavorito = $animeFavoritoItem?->anime;

                // 2️⃣ Nota media
                $notaMedia = $items->avg('score');

                // 3️⃣ Géneros favoritos (top 3)
                $animeIds = $items->pluck('anime_id');

                $animesVistos = Anime::whereIn('id', $animeIds)->get();

                $generosCount = [];

                foreach ($animesVistos as $anime) {
                    if (!$anime->genre) continue;

                    // Detectar si es JSON
                    $generos = json_decode($anime->genre, true);
                    if (!is_array($generos)) {
                        // Si no es JSON, asumimos string separado por comas
                        $generos = explode(',', $anime->genre);
                    }

                    foreach ($generos as $g) {
                        $g = trim($g);
                        if ($g) {
                            $generosCount[$g] = ($generosCount[$g] ?? 0) + 1;
                        }
                    }
                }

                arsort($generosCount);
                $top3 = array_slice($generosCount, 0, 3, true);

                $generosFavoritos = collect(array_keys($top3));
            }
        }


        /* ==========================================================
   PERSONAJE FAVORITO
========================================================== */
        $characterFavorito = null;

        $characterItems = CharacterListItem::whereHas(
            'list',
            fn($q) =>
            $q->where('user_id', $user->id)
        )
            ->with('character')
            ->get();

        if ($characterItems->count() > 0) {

            // Orden correcto: primero score DESC, luego el más reciente
            $characterFavoritoItem = $characterItems
                ->sortBy([
                    ['score', 'desc'],
                    ['created_at', 'desc'],
                ])
                ->first();

            $characterFavorito = $characterFavoritoItem?->character;
        }

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
            'totalVistos',
            'animeFavorito',
            'notaMedia',
            'generosFavoritos',
            'characterFavorito'
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
            'is_public' => 'required|boolean',
        ]);

        // Actualizar avatar
        if ($request->hasFile('avatar')) {

            // Si el avatar actual NO es el default, lo borramos
            if ($user->avatar && $user->avatar !== 'avatars/default-avatar.png') {
                Storage::disk('public')->delete($user->avatar);
            }

            // Guardar nuevo
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        // Actualizar banner
        if ($request->hasFile('banner')) {

            // Si el banner actual NO es el default, lo borramos
            if ($user->banner && $user->banner !== 'default-banner.jpg') {
                Storage::disk('public')->delete($user->banner);
            }

            // Guardar nuevo
            $user->banner = $request->file('banner')->store('banners', 'public');
        }

        // Actualizar biografía
        $user->bio = $request->bio;

        // Estado del perfil
        $user->is_public = $request->boolean('is_public');
        $user->save();

        // Sincronizar favoritos con el estado del perfil
        \DB::table('anime_favorites')
            ->where('user_id', $user->id)
            ->update(['is_public' => $user->is_public]);

        \DB::table('character_favorites')
            ->where('user_id', $user->id)
            ->update(['is_public' => $user->is_public]);

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
