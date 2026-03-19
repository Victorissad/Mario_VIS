<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ToadController;
use App\Http\Controllers\LoginController;

Route::get('/', function () {
    return redirect('/films');
});

// Routes d'authentification
Route::get('/login', [LoginController::class, 'showLogin']);
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout']);

// Routes protégées (nécessitent d'être connecté)
Route::middleware(\App\Http\Middleware\CheckLogin::class)->group(function () {

    // Films (create avant {id} pour éviter le conflit de route)
    Route::get('/films/create',    [ToadController::class, 'showCreateFilm']);
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
