<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Master\BumdesController;
use App\Http\Controllers\Master\DesaController;
use App\Http\Controllers\Master\KecamatanController;
use App\Http\Controllers\ServiceController;
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

Route::get('/', function () {
    return view('welcome');
});


// Auth Routes (Login)
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');



Route::middleware(['auth'])->group(function () {
    // Definisi rute dashboard umum
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::prefix('service')->group(function () {
        Route::get('/get-desa/{id}', [ServiceController::class, 'getDataDesa']);
   
    });


    // ✅ DASHBOARD SUPER ADMIN
    Route::prefix('admin')->middleware('role:administrator-sistem')->group(function () {
        Route::get('/master/kecamatan', [KecamatanController::class, 'index']);
        Route::get('/master/get-kecamatan', [KecamatanController::class, 'getKecamatan']);
        Route::get('/master/kecamatan-by-id/{id}', [KecamatanController::class, 'getData']);
        Route::post('/master/kecamatan', [KecamatanController::class, 'store']);
        Route::delete('/master/kecamatan/{id}', [KecamatanController::class, 'destroy']);

        Route::get('/master/desa', [DesaController::class, 'index']);
        Route::get('/master/get-desa', [DesaController::class, 'getDesa']);
        Route::get('/master/desa-by-id/{id}', [DesaController::class, 'getData']);
        Route::post('/master/desa', [DesaController::class, 'store']);
        Route::delete('/master/desa/{id}', [DesaController::class, 'destroy']);

        Route::get('/master/bumdes', [BumdesController::class, 'index']);
        Route::get('/master/get-bumdes', [BumdesController::class, 'getBumdes']);
        Route::get('/master/bumdes-by-id/{id}', [BumdesController::class, 'getData']);
        Route::post('/master/bumdes', [BumdesController::class, 'store']);
        Route::delete('/master/bumdes/{id}', [BumdesController::class, 'destroy']);
    });
});
