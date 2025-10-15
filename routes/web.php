<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AnimeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');


//Rutas Perfil
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/destroy', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
//Ruta para actualizar biografía y avatar
Route::post('/profile/update-bio-avatar', [ProfileController::class, 'updateBioAvatar'])
    ->middleware('auth')
    ->name('profile.updateBioAvatar');

//Rutas  Animes
Route::get('/animes', [AnimeController::class, 'index'])->name('animes.index');
//Route::get('/animes/search', [AnimeController::class, 'search'])->name('animes.search');

//Rutas Listas
Route::get('/listas', [AnimeController::class, 'index'])->name('listas.index');


require __DIR__.'/auth.php';
