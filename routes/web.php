<?php
use App\Http\Controllers\AssistantController;
use Illuminate\Support\Facades\Route;

//Route::get('/', function () {
   // return view('welcome');

Route::get('/', [AssistantController::class, 'index'])->name('assistant.index');
Route::post('/chat', [AssistantController::class, 'chat'])->name('assistant.chat');
Route::get('/history', [AssistantController::class, 'history'])->name('assistant.history');    
//});
