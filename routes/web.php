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


Route::middleware(['auth', 'verified'])->group(
    function () {
        Route::get('/heartbeat', [ChatController::class, 'heartbeat'])->name('heartbeat');
        Route::post('/api/friends', [ChatController::class, 'friendsAPI'])->name('api.friends.getter');
    }
);

require __DIR__.'/auth.php';
