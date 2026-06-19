<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeportesController;

Route::get('/', [DeportesController::class, 'index'])->name('home');
Route::get('/deportes/futbol', [DeportesController::class, 'futbol'])->name('deportes.futbol');
Route::get('/deportes/baloncesto', [DeportesController::class, 'baloncesto'])->name('deportes.baloncesto');
Route::get('/deportes/tenis', [DeportesController::class, 'tenis'])->name('deportes.tenis');

