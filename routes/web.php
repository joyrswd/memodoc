<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MemoController;
use App\Http\Controllers\PartsController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ApiJobController;
use App\Http\Controllers\UserController;

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
Route::get('/', function () {return view('login.index');})->name('home');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/about', [LoginController::class, 'about'])->name('about');

Route::middleware('guest')->group(function () {
    Route::post('/login', [LoginController::class, 'login'])->name('login');
    Route::get('/user/entry', [UserController::class, 'create'])->name('user.create');
    Route::post('/user/register', [UserController::class, 'store'])->name('user.store');
    Route::group(['prefix' => 'password', 'as' => 'password.'], function () {
        Route::get('request', [LoginController::class, 'passwordRequest'])->name('request');
        Route::post('email', [LoginController::class, 'passwordEmail'])->name('email');
        Route::get('reset/{token}', [LoginController::class, 'passwordReset'])->name('reset');
        Route::post('reset', [LoginController::class, 'passwordUpdate'])->name('update');
    });
});

Route::group(['middleware' => 'not.verified', 'prefix' => 'email', 'as' => 'verification.'], function () {
    Route::get('notice', [LoginController::class, 'emailNotice'])->name('notice');
    Route::get('verify/{id}/{hash}', [LoginController::class, 'emailVerify'])->middleware('signed')->name('verify');
    Route::post('resend', [LoginController::class, 'emailResend'])->name('resend');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('memo', MemoController::class)->except(['show']);
    Route::get('/parts/', [PartsController::class, 'index'])->name('parts.index');
    Route::post('/parts/', [PartsController::class, 'update'])->name('parts.update');
    Route::put('/parts/{memo}', [PartsController::class, 'add'])->name('parts.add');
    Route::delete('/parts/{memo?}', [PartsController::class, 'remove'])->name('parts.remove');
    Route::resource('doc', DocumentController::class)->except(['show', 'create', 'store']);
    Route::get('/job/', [ApiJobController::class, 'index'])->name('job.index');
    Route::post('/job/store', [ApiJobController::class, 'store'])->name('job.store');
    Route::delete('/job/{job}', [ApiJobController::class, 'destroy'])->name('job.destroy');
});
