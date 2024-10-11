<?php

use App\Http\Controllers\WatchlistAPIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::put('setStatus', [WatchlistAPIController::class, 'setStatus'])->name('api.setstatus');

Route::put('setNotes', [WatchlistAPIController::class, 'setNotes'])->name('api.setnotes');
