<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/chat', [ChatController::class, 'render'])  
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


Route::get('/heartbeat', [ChatController::class, 'heartbeat'])
    ->name('heartbeat')
    ->middleware(['auth', 'verified']);
require __DIR__.'/auth.php';
