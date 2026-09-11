<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GeometriController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\HomeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('geometri')->group(function () {
    Route::get('all', [GeometriController::class, 'all']);
    Route::get('', [GeometriController::class, 'index']);
    Route::post('', [GeometriController::class, 'store']);
    Route::post('AddRiwayat', [GeometriController::class, 'addRiwayat']);
    Route::post('/check-auth', [GeometriController::class, 'checkAuth']);
    Route::post('/get-user-data', [GeometriController::class, 'getUserData']);
    Route::put('{id}', [GeometriController::class, 'update']);
    Route::delete('{id}', [GeometriController::class, 'destroy']);
    Route::get('get-data/{tipe}/{geometri_id}', [GeometriController::class, 'get_data']);
    Route::post('import-geojson/lp2b', [GeometriController::class, 'import_lp2b']);
    Route::post('import-geojson/lsd', [GeometriController::class, 'import_lsd']);
    Route::get('/kecamatan', [GeometriController::class, 'getKecamatan']);
    Route::get('/desa/{kecamatanId}', [GeometriController::class, 'getDesa']);
});


Route::prefix('home')->group(function () {
    Route::get('getdata/{tipe}/{lat}/{long}', [HomeController::class, 'get']);
    Route::get('getdata', [HomeController::class, 'get']);
});

Route::prefix('dashboard')->group(function () {
    Route::get('', [DashboardController::class, 'index']);
    Route::get('pie-chart', [DashboardController::class, 'pieChart']);
    Route::get('desa/{kecamatan_id}', [DashboardController::class, 'getDesa']);
    Route::get('kecamatan', [DashboardController::class, 'getKecamatan']);
});
