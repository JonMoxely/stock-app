<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\StockController;

Route::prefix('stocks')->group(function () {
    Route::get('/compare', [StockController::class, 'compareDates']);
    Route::get('/period', [StockController::class, 'getByPeriod']);
});


