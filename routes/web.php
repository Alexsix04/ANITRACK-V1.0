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
    Route::post('/favorites/character', [CharacterFavoriteController::class, 'store'])->name('favorites.character.store');
    Route::delete('/favorites/character/{character_id}', [CharacterFavoriteController::class, 'destroy'])->name('favorites.character.destroy');
});


//Rutas Listas
Route::get('/listas', [AnimeController::class, 'index'])->name('listas.index');


require __DIR__.'/auth.php';
