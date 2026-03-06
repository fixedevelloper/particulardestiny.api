<?php


use App\Http\Controllers\AuthController;
use App\Http\Controllers\FRONT\CategoryController;
use App\Http\Controllers\FRONT\FeatureController;
use App\Http\Controllers\FRONT\HookController;
use App\Http\Controllers\FRONT\ReservationController;
use App\Http\Controllers\FRONT\RoomController;
use Illuminate\Support\Facades\Route;

// -------------------------------------------
// FRONT-END (public ou application mobile)
// -------------------------------------------
Route::prefix('front')->group(function () {
    // Public endpoints
    Route::get('/rooms', [RoomController::class, 'index']); // Liste des chambres
    Route::get('/rooms/{slug}', [RoomController::class, 'show']); // Détails chambre
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/features', [FeatureController::class, 'index']);
    Route::post('/rooms', [RoomController::class, 'roomSearch']); // Liste des chambres
    Route::get('/countries', [HookController::class, 'countries']);
    // Auth Front
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    // Routes protégées par Sanctum
   // Route::middleware('auth:sanctum')->group(function () {
        Route::post('/reservations', [ReservationController::class, 'store']);
        Route::get('/reservations', [ReservationController::class, 'userReservations']);
        Route::post('/logout', [AuthController::class, 'logout'])->name('login');
  //  });
});
