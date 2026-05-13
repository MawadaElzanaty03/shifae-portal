<?php

use Illuminate\Support\Facades\Route;

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
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DoctorController;
// لعرض صفحة تسجيل الدخول
Route::get('/login', function () {
    return view('login');
})->name('login');





// لاستقبال البيانات من الفورم والتحقق منها
Route::post('/login', [AuthController::class, 'login']);

// لتسجيل الخروج
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', [DoctorController::class, 'index'])->name('home');


main
