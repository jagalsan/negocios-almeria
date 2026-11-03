<?php

use App\Http\Controllers\Api\V1\DirectoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('throttle:60,1')->group(function () {
    Route::get('/rankings', [DirectoryController::class, 'rankings']);
    Route::get('/rankings/{slug}', [DirectoryController::class, 'ranking']);
    Route::get('/companies', [DirectoryController::class, 'companies']);
    Route::get('/companies/{slug}', [DirectoryController::class, 'company']);
    Route::get('/search', [DirectoryController::class, 'search']);
    Route::post('/events', [DirectoryController::class, 'event'])->middleware('throttle:30,1');
    Route::get('/categories', [DirectoryController::class, 'categories']);
    Route::get('/municipalities', [DirectoryController::class, 'municipalities']);
});
