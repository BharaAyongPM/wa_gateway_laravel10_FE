<?php

use App\Http\Controllers\AutoReplyController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\ProfileController;
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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::prefix('admin/device')->group(function () {
        Route::get('/', [DeviceController::class, 'index'])->name('device.index');
        Route::post('/create', [DeviceController::class, 'create'])->name('device.create');
        Route::get('/{id}/qr', [DeviceController::class, 'showQr'])->name('device.qr');
        Route::get('/{id}/status', [DeviceController::class, 'checkStatus'])->name('device.status');
        Route::delete('/{id}', [DeviceController::class, 'destroy'])->name('device.destroy');
    });
    Route::resource('auto-reply', AutoReplyController::class);
    Route::get('/log', [DeviceController::class, 'log'])->name('log');
    Route::post('/device/{id}/generate-apikey', [DeviceController::class, 'generateApiKey'])->name('device.generate-apikey');
    Route::get('/api/device/{id}/qrcode-live', [DeviceController::class, 'liveQr']);
});

// Route::get('/', function () {
//     return view('dashboard');
// })->name('admin.dashboard');



require __DIR__ . '/auth.php';
