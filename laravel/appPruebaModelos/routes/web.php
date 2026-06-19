<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeporteController;

// Ruta principal - Lista de deportes
Route::get('/', [DeporteController::class, 'index'])->name('home');

// Rutas del recurso deportes
Route::resource('deportes', DeporteController::class);

// Ruta personalizada para mostrar deporte por slug
Route::get('/deporte/{slug}', [DeporteController::class, 'show'])->name('deportes.slug');
