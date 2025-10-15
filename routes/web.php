<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AnimeController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;


//Rutas para la vista principal
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/home', [HomeController::class, 'index']); // Alias opcional

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
Route::get('/animes/{id}', [AnimeController::class, 'show'])->name('animes.show');

//Rutas Listas
Route::get('/listas', [AnimeController::class, 'index'])->name('listas.index');


require __DIR__.'/auth.php';
