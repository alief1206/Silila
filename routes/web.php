<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogController;
use App\Http\Controllers\LsdController;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\DesaController;
use App\Http\Controllers\Lp2bController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\HomeUserController;
use App\Http\Controllers\KecamatanController;
use App\Http\Controllers\GeometriAdminController;
use App\Http\Controllers\ChatController;
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

// Route::get('/', function () {
//     return view('home');
// });

Route::get('/secrets/optimize', function () {
    Artisan::call('optimize');
    return response()->json(['message' => 'Application optimized successfully']);
});
Route::get('/secrets/route-clear', function () {
    Artisan::call('route:clear');
    return response()->json(['message' => 'Application route cleared successfully']);
});
Route::get('/secrets/storage-link', function () {
    Artisan::call('storage:link');
    return response()->json(['message' => 'Storage link created successfully']);
});



Route::prefix('dashboard')->middleware(['auth', 'isAdmin:1,3', 'check.activity'])->group(function () {
    // Rute untuk halaman utama dashboard
    Route::get('/', function () {
        return view('dashboard.main');
    })->name('dashboard.index');

    Route::middleware(['isAdmin:1'])->group(function () {
        Route::get('geometri', [GeometriAdminController::class, 'index'])->name('dashboard.geometri');
        Route::get('lp2b', [Lp2bController::class, 'index'])->name('dashboard.lp2b');
        Route::get('lsd', [LsdController::class, 'index'])->name('dashboard.lsd');
    });

    Route::middleware(['role:superadmin,admin'])->group(function () {
        Route::get('log', [App\Http\Controllers\LogController::class, 'index'])->name('dashboard.log');
        Route::get('history', [App\Http\Controllers\HistoryController::class, 'index'])->name('dashboard.history');
        Route::get('chat', [ChatController::class, 'adminIndex'])->name('dashboard.chat');
        Route::get('chat/sessions', [ChatController::class, 'adminGetSessions'])->name('dashboard.chat.sessions');
        Route::post('chat/sessions/{id}/close', [ChatController::class, 'adminCloseSession'])->name('dashboard.chat.close');
    });

    Route::get('profile', function () {
        return view('dashboard.profile');
    })->name('dashboard.profile');
});

Route::get('print/{geometri_id}', function ($geometri_id) {
    return view('dashboard.print', ['geometri_id' => $geometri_id]);
})->name('print')->middleware('auth');


// Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


// Route::prefix('dashboardGeometri')->group(function () {
//     Route::get('all', [GeometriAdminController::class, 'all']);
//     Route::get('/', [GeometriAdminController::class, 'index']);
//     Route::post('', [GeometriAdminController::class, 'store']);
//     Route::put('{id}', [GeometriAdminController::class, 'update']);
//     Route::delete('{id}', [GeometriAdminController::class, 'destroy']);
// });


//dashboard geometri
Route::post('add-geometri', [GeometriAdminController::class, 'store'])->name("add-geometri")->middleware('auth');
Route::put('update-geometri/{id}', [GeometriAdminController::class, 'update'])->name('update-geometri')->middleware('auth');
Route::get('delete-geometri/{id}', [GeometriAdminController::class, 'destroy'])->name('delete-geometri')->middleware('auth');

//Dashboard LP2B
Route::get('dashboardLp2b', [Lp2bController::class, 'index'])->name('dashboardLsd')->middleware('auth');
Route::post('add-lp2b', [Lp2bController::class, 'store'])->name("add-lp2b")->middleware('auth');
Route::get('delete-lp2b/{id}', [Lp2bController::class, 'destroy'])->name('delete-lp2b')->middleware('auth');
Route::put('update-lp2b/{id}', [Lp2bController::class, 'update'])->name('update-lp2b')->middleware('auth');

//Dashboard LSD
Route::post('add-lsd', [LsdController::class, 'store'])->name("add-lsd")->middleware('auth');
Route::get('delete-lsd/{id}', [LsdController::class, 'destroy'])->name('delete-lsd')->middleware('auth');
Route::put('update-lsd/{id}', [LsdController::class, 'update'])->name('update-lsd')->middleware('auth');

//Dashboard Desa
Route::get('dashboardDesa', [DesaController::class, 'index'])->name('dashboardDesa')->middleware('auth');
Route::post('add-desa', [DesaController::class, 'store'])->name("add-desa")->middleware('auth');
Route::get('delete-desa/{id}', [DesaController::class, 'destroy'])->name('delete-desa')->middleware('auth');
Route::put('update-desa/{id}', [DesaController::class, 'update'])->name('update-desa')->middleware('auth');

//Dashboard Kecamatan
Route::get('dashboardKecamatan', [KecamatanController::class, 'index'])->name('dashboardKecamatan')->middleware('auth');
Route::post('add-kecamatan', [KecamatanController::class, 'store'])->name("add-kecamatan")->middleware('auth');
Route::get('delete-kecamatan/{id}', [KecamatanController::class, 'destroy'])->name('delete-kecamatan')->middleware('auth');
Route::put('update-kecamatan/{id}', [KecamatanController::class, 'update'])->name('update-kecamatan')->middleware('auth');





Route::resource('profile', 'App\Http\Controllers\ProfileController')->middleware('auth');


Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::post('/chatbot/message', [App\Http\Controllers\ChatbotController::class, 'respond'])->name('chatbot.message');

Route::post('/chat/start', [ChatController::class, 'startSession'])->name('chat.start');
Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
Route::get('/chat/{sessionId}/messages', [ChatController::class, 'fetchMessages'])->name('chat.messages');
Route::post('/chat/{sessionId}/read', [ChatController::class, 'markAsRead'])->name('chat.read');
