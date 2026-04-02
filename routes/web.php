<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ToadController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;

Route::get('/', function () {
    return redirect('/films');
});

// Routes d'authentification
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegister']);
    Route::post('/register', [RegisterController::class, 'register']);
});
Route::post('/logout', [LoginController::class, 'logout']);

// Routes protégées (nécessitent d'être connecté)
Route::middleware('auth')->group(function () {

    // Films (create avant {id} pour éviter le conflit de route)
    Route::get('/films/create',    [ToadController::class, 'showCreateFilm']);
    Route::post('/films/data',     [ToadController::class, 'getFilmsData'])->name('films.data');
    Route::post('/films',          [ToadController::class, 'createFilm']);
    Route::get('/films',           [ToadController::class, 'getFilms']);
    Route::get('/films/{id}/edit', [ToadController::class, 'showEditFilm']);
    Route::put('/films/{id}',      [ToadController::class, 'updateFilm']);
    Route::delete('/films/{id}',   [ToadController::class, 'deleteFilm']);
    Route::get('/films/{id}',      [ToadController::class, 'getFilmDetail']);

    // Inventaires / Stock
    Route::post('/inventories',          [ToadController::class, 'createInventory']);
    Route::get('/inventories',           [ToadController::class, 'getInventories']);
    Route::put('/inventories/{id}',      [ToadController::class, 'updateInventory']);
    Route::delete('/inventories/{id}',   [ToadController::class, 'deleteInventory']);
    Route::get('/inventories/{id}',      [ToadController::class, 'getInventoryDetail']);

});
