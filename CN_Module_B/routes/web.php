<?php

use App\Models\Station;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route("board.index");
});


//board
Route::get("/board", [\App\Http\Controllers\BoardController::class, "index"])->name("board.index");

//station code
Route::get("/board/{stationCode}", [\App\Http\Controllers\BoardController::class, 'show'])->name("board.show");


//stats
Route::get("/stats", [\App\Http\Controllers\BoardController::class, "stats"])->name("stats.index");
