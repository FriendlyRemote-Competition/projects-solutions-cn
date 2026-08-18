<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\LineController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


//admin router
Route::prefix('admin')->group(function () {
//    login
    Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);

//    auth
    Route::middleware(\App\Http\Middleware\AuthMiddleware::class)->group(function () {
        Route::get("/bookings",[\App\Http\Controllers\AdminController::class,"bookings"]);
        Route::post("/lines",[\App\Http\Controllers\AdminController::class,"lines"]);
        Route::put("/lines/{line:line_code}",[\App\Http\Controllers\AdminController::class,"updateLine"]);
    });

});


//lines

Route::prefix('lines')->group(function () {
//    list
    Route::get("/", [LineController::class, 'index']);
//    detail
    Route::get("/{line:line_code}", [LineController::class, 'show']);
//    timetable
    Route::get("/{line:line_code}/timetable", [LineController::class, 'timetable']);
});


Route::prefix("bookings")->group(function () {

    Route::post("/", [BookingController::class, 'store']);
    Route::post("/lookup", [BookingController::class, 'lookup']);
    Route::patch("/{code}", [BookingController::class, 'update']);
    Route::post("/{code}/cancel", [BookingController::class, 'cancel']);
});
