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
    Route::get('/films', [ToadController::class, 'getFilms']);
    Route::get('/films/{id}', [ToadController::class, 'getFilmDetail']);
    Route::get('/inventories', [ToadController::class, 'getInventories']);
    Route::get('/inventories/{id}', [ToadController::class, 'getInventoryDetail']);
    Route::get('/stores', [ToadController::class, 'getStores']);
    Route::get('/stores/{id}', [ToadController::class, 'getStoreDetail']);
});
