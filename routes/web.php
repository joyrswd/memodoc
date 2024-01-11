<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MemoController;
use App\Http\Controllers\PartsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// ログイン画面表示
Route::get('/',function(){return view('login.index');})->name('home');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function(){
    Route::resource('memo', MemoController::class)->except(['show']);
    Route::get('/parts/', [PartsController::class, 'index'])->name('parts.index');
    Route::put('/parts/{memo}', [PartsController::class, 'add'])->name('parts.add');
    Route::delete('/parts/{memo?}', [PartsController::class, 'remove'])->name('parts.remove');
});

