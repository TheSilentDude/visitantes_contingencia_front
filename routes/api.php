<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (Frontend)
|--------------------------------------------------------------------------
|
| El frontend generalmente no expone APIs propias, consume el backend API.
| Este archivo existe para evitar errores de Laravel.
|
*/

// Ruta de ejemplo (opcional)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
