<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AssistantController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/assistant', [AssistantController::class, 'index'])->name('assistant.index');
    Route::post('/chat', [AssistantController::class, 'chat'])->name('assistant.chat');
    Route::get('/history', [AssistantController::class, 'history'])->name('assistant.history');
});

require __DIR__.'/auth.php';
