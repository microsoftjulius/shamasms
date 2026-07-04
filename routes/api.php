<?php

use App\Http\Controllers\Api\V1\SmsController as V1SmsController;
use App\Http\Controllers\Api\V2\SmsController as V2SmsController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('/sms/send', [V1SmsController::class, 'send']);
    Route::post('/balance', [V1SmsController::class, 'balance']);
});

Route::prefix('v2')->group(function (): void {
    Route::post('/sms/send', [V2SmsController::class, 'send']);
    Route::get('/balance', [V2SmsController::class, 'balance']);
});
