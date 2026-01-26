<?php

use App\Http\Controllers\Clients\APIs\V1\PayOSController;
use App\Http\Controllers\Clients\ShopController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// PayOS Webhook
Route::post('/v1/payos/webhook', [PayOSController::class, 'webhook'])->name('api.payos.webhook');

// Shop Search API
Route::get('/shop/search', [ShopController::class, 'search'])->name('api.shop.search');
