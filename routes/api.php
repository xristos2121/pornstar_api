<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PornstarController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Pornstar API endpoints
Route::prefix('v1')->group(function () {
    Route::get('/pornstars', [PornstarController::class, 'index'])->name('pornstars.index');
    Route::get('/pornstars/search', [PornstarController::class, 'search'])->name('pornstars.search');
    Route::get('/pornstars/{id}', [PornstarController::class, 'show'])->name('pornstars.show');
});
