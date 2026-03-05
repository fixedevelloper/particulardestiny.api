<?php


use App\Http\Controllers\ADMIN\CategoryController;
use App\Http\Controllers\ADMIN\FeatureController;
use App\Http\Controllers\ADMIN\ImageController;
use App\Http\Controllers\ADMIN\ReservationController;
use App\Http\Controllers\ADMIN\RoomController;
use App\Http\Controllers\ADMIN\RoomTypeController;
use App\Http\Controllers\ADMIN\UserController;
use App\Http\Controllers\FRONT\DashboardController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

// -------------------------------------------
// BACK-END (admin / manager panel)
// -------------------------------------------
//Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::apiResource('rooms', RoomController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('room-types', RoomTypeController::class);
    Route::apiResource('features', FeatureController::class);
    Route::apiResource('reservations', ReservationController::class);
    Route::apiResource('payments', PaymentController::class);
    Route::apiResource('images', ImageController::class);
    // Gestion des utilisateurs
    Route::get('/users', [UserController::class, 'index']);
});
