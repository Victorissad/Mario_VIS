<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ToadController;

Route::get('/', function () {
    return view('welcome');
});

// Routes pour afficher les données depuis l'API Toad
Route::get('/films', [ToadController::class, 'getFilms']);
Route::get('/films/{id}', [ToadController::class, 'getFilmDetail']);
Route::get('/inventories', [ToadController::class, 'getInventories']);
Route::get('/inventories/{id}', [ToadController::class, 'getInventoryDetail']);
Route::get('/stores', [ToadController::class, 'getStores']);
Route::get('/stores/{id}', [ToadController::class, 'getStoreDetail']);
