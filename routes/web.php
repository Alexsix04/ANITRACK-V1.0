<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AnimeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CharactersController;
use App\Http\Controllers\EpisodesController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\VoiceActorsController;
use App\Http\Controllers\AnimeCommentController;
use App\Http\Controllers\CharacterCommentController;
use App\Http\Controllers\AnimeFavoriteController;
use App\Http\Controllers\CharacterFavoriteController;
use App\Http\Controllers\AnimeListController;
use Illuminate\Support\Facades\Route;


//Rutas para la vista principal
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/home', [HomeController::class, 'index']); // Alias opcional

//Rutas Perfil
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/destroy', [ProfileController::class, 'destroy'])
    ->where('seccion', '.*')
    ->name('profile.destroy');
});
//Ruta para actualizar biografía y avatar
Route::post('/profile/update-bio-avatar', [ProfileController::class, 'updateBioAvatar'])
    ->middleware('auth')
    ->name('profile.updateBioAvatar');

//Rutas  Animes
Route::get('/animes', [AnimeController::class, 'index'])->name('animes.index');
Route::get('/animes/{id}', [AnimeController::class, 'show'])->name('animes.show');

// Secciones
Route::prefix('animes/{anime}')->name('animes.')->group(function () {
    Route::get('personajes', [CharactersController::class, 'index'])->name('characters.index');
    Route::get('personajes/{character}', [CharactersController::class, 'show'])->name('characters.show');
    Route::get('staff', [StaffController::class, 'index'])->name('staff.index');
    Route::get('staff/{staff}', [StaffController::class, 'show'])->name('staff.show');
    Route::get('episodios', [EpisodesController::class, 'index'])->name('episodes.index');
});

//Actores de voz
Route::get('animes/Actores-de-voz/{id}', [VoiceActorsController::class, 'show'])->name('animes.voiceactors.show');

//Rutas Comentarios
Route::post('/anime-comments', [AnimeCommentController::class, 'store'])->name('anime-comments.store');
Route::post('/anime-comments/{comment}/toggle-like', [AnimeCommentController::class, 'toggleLike'])->name('anime-comments.toggle-like');
Route::post('/character-comments', [CharacterCommentController::class, 'store'])->name('characters.comments.store');
Route::post('/character-comments/{comment}/toggle-like', [CharacterCommentController::class, 'toggleLike'])->name('characters.comments.toggle-like');

//Favoritos
Route::middleware(['auth'])->group(function () {
    Route::post('/favorites/anime', [AnimeFavoriteController::class, 'store'])->name('favorites.anime.store');
    Route::delete('/favorites/anime/{animeId}', [AnimeFavoriteController::class, 'destroy'])->name('favorites.anime.destroy');
    Route::post('/favorites/anime/toggle', [AnimeFavoriteController::class, 'toggleAnime'])->name('favorites.anime.toggle');
    Route::post('/favorites/character', [CharacterFavoriteController::class, 'store'])->name('favorites.character.store');
    Route::delete('/favorites/character/{character_id}', [CharacterFavoriteController::class, 'destroy'])->name('favorites.character.destroy');
    Route::post('/favorites/character/toggle', [CharacterFavoriteController::class, 'toggleCharacter'])->name('favorites.character.toggle');
});


//Rutas Listas
Route::get('/listas', [AnimeController::class, 'index'])->name('listas.index');

//Rutas de listas de usuario
Route::middleware(['auth'])->group(function () {
    // Listas personales del usuario (para profile.index)
    Route::get('/profile/lists', [AnimeListController::class, 'myLists'])->name('profile.lists');
    // Actualizar datos de un anime dentro de una lista (desde el perfil)
    Route::put('/anime-list/{item}/update', [AnimeListController::class, 'updateAnimeInList'])->name('anime-list.update');

    Route::post('/lists/create', [AnimeListController::class, 'create'])->name('lists.create');

    // Añadir o eliminar desde animes.show
    Route::post('/anime/add-to-list', [AnimeListController::class, 'addAnimeToList'])->name('anime.addToList');
    Route::delete('/anime/remove-from-list', [AnimeListController::class, 'removeFromList'])->name('anime.removeFromList');

    //Crear una lista nueva desde animes.show
    Route::post('/anime/list/create', [AnimeListController::class, 'store'])->name('anime.list.create');
});


require __DIR__.'/auth.php';
