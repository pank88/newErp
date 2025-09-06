<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\AccountingController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\AnalyticsController;

// Auth routes
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

// Protected routes
Route::middleware('auth:api')->group(function () {
    Route::get('profile', [AuthController::class, 'profile']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::apiResource('users', UserController::class);
    Route::apiResource('inventory', InventoryController::class);
    Route::apiResource('accounting', AccountingController::class);
    Route::get('dashboard/summary', [DashboardController::class, 'summary']);
    Route::get('reports/inventory', [ReportController::class, 'inventorySummary']);
    Route::get('reports/accounting', [ReportController::class, 'accountingSummary']);
    Route::get('analytics/trends', [AnalyticsController::class, 'trends']);
});
