<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AutoReplyController;
use App\Http\Controllers\BroadcastController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UpgradeController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Guest Routes (Login & Register)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});


/*
|--------------------------------------------------------------------------
| Dashboard Redirect After Login
|--------------------------------------------------------------------------
| Redirect user based on role (admin/user) => handled in controller
*/
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
Route::post('/payments/midtrans/notify', [PaymentController::class, 'notify'])->name('payments.midtrans.notify');
/*
|--------------------------------------------------------------------------
| Shared Authenticated Routes (All Roles)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // User Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/device/{device}/upgrade', [UpgradeController::class, 'show'])->name('device.upgrade.show');
    Route::post('/device/{device}/checkout', [UpgradeController::class, 'checkout'])
        ->name('device.upgrade.checkout')
        ->middleware('auth');
    Route::get('/payments/midtrans/finish', [PaymentController::class, 'finish'])->name('payments.midtrans.finish');


    /*
    |--------------------------------------------------------------------------
    | Admin-Only Routes (with Middleware)
    |--------------------------------------------------------------------------
    */
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('admin.users.index');
        Route::get('/perangkat', [AdminController::class, 'deviceList'])->name('admin.perangkat');
        // Device Management


        // Auto Reply
        // Route::resource('auto-reply', AutoReplyController::class);
        // Auto Reply Rules
        Route::get('autoreplies',           [AdminController::class, 'indexautoreply'])->name('admin.autoreplies.index');
        Route::post('autoreplies',          [AdminController::class, 'storeautoreply'])->name('admin.autoreplies.store');
        Route::get('autoreplies/{ar}',      [AdminController::class, 'fetchautoreply'])->name('admin.autoreplies.fetch');
        Route::put('autoreplies/{ar}',      [AdminController::class, 'updateautoreply'])->name('admin.autoreplies.update');
        Route::delete('autoreplies/{ar}',   [AdminController::class, 'destroyautoreply'])->name('admin.autoreplies.destroy');
        Route::patch('autoreplies/{ar}/toggle', [AdminController::class, 'toggleautoreply'])->name('admin.autoreplies.toggle');


        // Broadcast
        // Route::get('/broadcasts', [BroadcastController::class, 'index'])->name('broadcast.index');
        // Route::post('/broadcasts', [BroadcastController::class, 'store'])->name('broadcast.store');
        // Route::post('/broadcasts/toggle/{id}', [BroadcastController::class, 'toggle'])->name('broadcast.toggle');
        // Route::delete('/broadcast/{id}', [BroadcastController::class, 'destroy'])->name('broadcast.destroy');
        // Route::get('/api/broadcasts/check', [BroadcastController::class, 'checkForBroadcasts']);
        Route::get('broadcasts',                 [AdminController::class, 'indexbroadcast'])->name('admin.broadcasts.index');
        Route::post('broadcasts',                [AdminController::class, 'storebroadcast'])->name('admin.broadcasts.store');
        Route::get('broadcasts/{broadcast}',     [AdminController::class, 'fetchbroadcast'])->name('admin.broadcasts.fetch');
        Route::put('broadcasts/{broadcast}',     [AdminController::class, 'updatebroadcast'])->name('admin.broadcasts.update');
        Route::patch('broadcasts/{broadcast}/toggle', [AdminController::class, 'togglebroadcast'])->name('admin.broadcasts.toggle');
        Route::delete('broadcasts/{broadcast}',  [AdminController::class, 'destroybroadcast'])->name('admin.broadcasts.destroy');

        // Log
        Route::get('/log', [DeviceController::class, 'log'])->name('log');
        //plan
        Route::get('plans', [AdminController::class, 'indexplan'])->name('admin.plans.index');
        Route::post('plans', [AdminController::class, 'storeplan'])->name('admin.plans.store');
        Route::get('plans/{plan}', [AdminController::class, 'fetchplan'])->name('admin.plans.fetch');
        Route::put('plans/{plan}', [AdminController::class, 'updateplan'])->name('admin.plans.update');
        Route::delete('plans/{plan}', [AdminController::class, 'destroyplan'])->name('admin.plans.destroy');
        // optional: endpoint ambil data plan untuk modal edit via AJAX
        Route::get('plans/{plan}', [AdminController::class, 'fetchplan'])->name('admin.plans.fetch');

        //PAYMEN
        Route::get('payments',            [AdminController::class, 'indexpayment'])->name('admin.payments.index');
        Route::get('payments/summary',    [AdminController::class, 'summarypayment'])->name('admin.payments.summary');   // ringkas
        Route::get('payments/{payment}',  [AdminController::class, 'fetchpayment'])->name('admin.payments.fetch');       // JSON detail
        Route::put('payments/{payment}',  [AdminController::class, 'updatepayment'])->name('admin.payments.update');     // ubah status (opsional)


        Route::get('messages',              [AdminController::class, 'indexmessages'])->name('admin.messages.index');
        Route::get('messages/summary',      [AdminController::class, 'summarymessages'])->name('admin.messages.summary'); // JSON ringkas
        Route::get('messages/{message}',    [AdminController::class, 'fetchmessage'])->name('admin.messages.fetch');      // JSON detail
        Route::delete('messages/{message}', [AdminController::class, 'destroymessage'])->name('admin.messages.destroy'); // hapus log
        Route::post('messages/{message}/retry', [AdminController::class, 'retrysentmessage'])->name('admin.messages.retry'); // kirim ulang
    });

    //user
    Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
        Route::get('dashboard', [UserController::class, 'dashboard'])->name('dashboard');
        Route::get('/my/messages', [UserController::class, 'messagesHistory'])
            ->name('messages.history');
    });
    Route::prefix('device')->group(function () {
        Route::get('/', [DeviceController::class, 'index'])->name('device.index');
        Route::post('/create', [DeviceController::class, 'create'])->name('device.create');
        Route::get('/{id}/qr', [DeviceController::class, 'showQr'])->name('device.qr');
        Route::get('/{id}/status', [DeviceController::class, 'checkStatus'])->name('device.status');
        Route::delete('/{id}', [DeviceController::class, 'destroy'])->name('device.destroy');
        Route::post('/{id}/generate-apikey', [DeviceController::class, 'generateApiKey'])->name('device.generate-apikey');
        Route::get('/{id}/qrcode-live', [DeviceController::class, 'liveQr']);
    });
    //kirim pesan
    Route::get('/messages', [MessagesController::class, 'index'])->name('messages.index');
    Route::get('/messages/groups', [MessagesController::class, 'groupsByDevice'])->name('messages.groups'); // GET dengan ?device_id=
    Route::post('/messages/send', [MessagesController::class, 'store'])->name('messages.store');
});
Route::get('bot/settings', [AutoReplyController::class, 'indexbot'])->name('bot.settings');
Route::post('bot/settings', [AutoReplyController::class, 'updatebot'])->name('bot.settings.update');

/*
|--------------------------------------------------------------------------
| Laravel Breeze/Fortify Auth Routes (default)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
