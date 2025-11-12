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
use App\Http\Controllers\CharacterListController;
use App\Http\Controllers\ListsController;
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


// Rutas de listas
Route::prefix('listas')->group(function () {

    // Índice de listas (landing page)
    Route::get('/', [ListsController::class, 'index'])->name('listas.index');

    // === Anime ===
    Route::get('/anime/public', [ListsController::class, 'publicAnimeLists'])->name('listas.anime.public');
    Route::post('/anime/{list}/guardar', [ListsController::class, 'saveAnimeList'])->middleware('auth')->name('listas.anime.save');
    Route::get('/anime/guardadas', [ListsController::class, 'savedAnimeLists'])->middleware('auth')->name('listas.anime.saved');

    // === Personajes ===
    Route::get('/characters/public', [ListsController::class, 'publicCharacterLists'])->name('listas.characters.public');
    Route::post('/characters/{list}/guardar', [ListsController::class, 'saveCharacterList'])->middleware('auth')->name('listas.characters.save');
    Route::get('/characters/guardadas', [ListsController::class, 'savedCharacterLists'])->middleware('auth')->name('listas.characters.saved');
});

//Rutas de listas de usuario
Route::middleware(['auth'])->group(function () {
    // Listas personales del usuario (para profile.index)
    Route::get('/profile/lists', [AnimeListController::class, 'myLists'])->name('profile.lists');
    // Actualizar datos de un anime dentro de una lista
    Route::put('/anime-list/{item}/update', [AnimeListController::class, 'updateAnimeInList'])->name('anime-list.update');

    Route::post('/lists/create', [AnimeListController::class, 'create'])->name('lists.create');

    // Añadir o eliminar desde animes.show
    Route::post('/anime/add-to-list', [AnimeListController::class, 'addAnimeToList'])->name('anime.addToList');
    Route::delete('/anime-list/{id}/delete', [AnimeListController::class, 'destroy'])->name('anime-list.delete');

    //Crear una lista nueva
    Route::post('/anime/list/create', [AnimeListController::class, 'store'])->name('anime.list.create');

    // Actualizar una lista 
    Route::put('/anime-lists/{list}/update', [AnimeListController::class, 'update'])->name('anime-lists.update');

    // Eliminar una lista
    Route::delete('/anime-lists/{list}/delete', [AnimeListController::class, 'delete'])->name('animeLists.delete');
});

Route::middleware(['auth'])->group(function () {
    // Listas de personajes
    Route::get('/character-lists/my', [CharacterListController::class, 'myLists'])->name('characterlists.my');
    Route::post('/character-lists/create', [CharacterListController::class, 'create'])->name('characterlists.create');
    Route::post('/character-lists/store', [CharacterListController::class, 'store'])->name('characterlists.store');
    // Actualizar una lista de personajes
    Route::put('/character-lists/{characterList}/update', [CharacterListController::class, 'update']);
    // Eliminar una lista de personajes
    Route::delete('/character-lists/{characterList}/delete', [CharacterListController::class, 'delete']);

    // Añadir personaje a una lista
    Route::post('/character-lists/add', [CharacterListController::class, 'addCharacterToList'])->name('characterlists.add');

    // Actualizar y eliminar
    Route::put('/character-lists/item/{item}', [CharacterListController::class, 'updateCharacterInList'])->name('characterlists.update');
    Route::delete('/character-lists/item/{id}', [CharacterListController::class, 'destroy'])->name('characterlists.destroy');

    Route::post('/character/add-to-list', [CharacterListController::class, 'addCharacterToList'])->name('character.addToList');

    Route::post('/character-lists/create', [CharacterListController::class, 'store'])->name('character.list.create');

    Route::post('/character-list/update', [CharacterListController::class, 'updateItem'])->name('character.list.update');
});

require __DIR__ . '/auth.php';
