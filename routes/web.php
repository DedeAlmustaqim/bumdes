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

        Route::get('/master/opd', [App\Http\Controllers\Master\OpdController::class, 'index']);
        Route::get('/master/get-opd', [App\Http\Controllers\Master\OpdController::class, 'getOpd']);
        Route::get('/master/opd-by-id/{id}', [App\Http\Controllers\Master\OpdController::class, 'getData']);
        Route::post('/master/opd', [App\Http\Controllers\Master\OpdController::class, 'store']);
        Route::delete('/master/opd/{id}', [App\Http\Controllers\Master\OpdController::class, 'destroy']);

        Route::get('/user/petugas', [App\Http\Controllers\UserController::class, 'userPetugas']);
        Route::get('/user/get-datatables-petugas', [App\Http\Controllers\UserController::class, 'getDatatablesPetugas']);
        Route::get('/user/get-petugas-by-id/{id}', [App\Http\Controllers\UserController::class, 'showPetugas']);
        Route::post('/user/petugas', [App\Http\Controllers\UserController::class, 'storePetugas']);
        Route::delete('/user/petugas/{id}', [App\Http\Controllers\UserController::class, 'destroyPetugas']);

        Route::get('/user/operator-bumdes', [App\Http\Controllers\UserController::class, 'userOpBumdes']);
        Route::get('/user/get-datatables-operator-bumdes', [App\Http\Controllers\UserController::class, 'getDatatablesOpBumdes']);
        Route::get('/user/get-operator-bumdes-by-id/{id}', [App\Http\Controllers\UserController::class, 'showOpBumdes']);
        Route::post('/user/operator-bumdes', [App\Http\Controllers\UserController::class, 'storeOpBumdes']);
        Route::delete('/user/del-user-operator-bumdes/{id}', [App\Http\Controllers\UserController::class, 'destroyOpBumdes']);
   
      Route::get('/user/operator-opd', [App\Http\Controllers\UserController::class, 'userOpOpd']);
        Route::get('/user/get-datatables-operator-opd', [App\Http\Controllers\UserController::class, 'getDatatablesOpOpd']);
        Route::get('/user/get-operator-opd-by-id/{id}', [App\Http\Controllers\UserController::class, 'showOpOpd']);
        Route::post('/user/operator-opd', [App\Http\Controllers\UserController::class, 'storeOpOpd']);
        Route::delete('/user/del-user-operator-opd/{id}', [App\Http\Controllers\UserController::class, 'destroyOpOpd']);
        });

    Route::prefix('bumdes')->middleware('role:operator-bumdes')->group(function () {
    
    
    });
});
