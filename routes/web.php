<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/hotels', [HotelController::class, 'index'])->name('hotels.index');
Route::get('/hotels/{hotel}', [HotelController::class, 'show'])->name('hotels.show');

Route::middleware('auth')->prefix('bookings')->name('bookings.')->group(function () {
    Route::get('/', [BookingController::class, 'index'])->name('index');
    Route::get('/hotels/{hotel}/rooms/{room}/create', [BookingController::class, 'create'])->name('create');
    Route::post('/hotels/{hotel}/rooms/{room}', [BookingController::class, 'store'])->name('store');
    Route::post('/{booking}/pay', [BookingController::class, 'pay'])->name('pay');
    Route::post('/{booking}/fail-payment', [BookingController::class, 'failPayment'])->name('fail-payment');
    Route::post('/{booking}/cancel', [BookingController::class, 'cancel'])->name('cancel');
    Route::get('/{booking}', [BookingController::class, 'show'])->name('show');
});

Route::middleware('auth')->prefix('manage')->name('manage.')->group(function () {
    Route::get('/hotels', [HotelController::class, 'manage'])->name('hotels.index');
    Route::get('/hotels/create', [HotelController::class, 'create'])->name('hotels.create');
    Route::post('/hotels', [HotelController::class, 'store'])->name('hotels.store');
    Route::get('/hotels/{hotel}/edit', [HotelController::class, 'edit'])->name('hotels.edit');
    Route::put('/hotels/{hotel}', [HotelController::class, 'update'])->name('hotels.update');
    Route::delete('/hotels/{hotel}', [HotelController::class, 'destroy'])->name('hotels.destroy');

    Route::get('/hotels/{hotel}/rooms', [RoomController::class, 'manage'])->name('rooms.index');
    Route::get('/hotels/{hotel}/rooms/create', [RoomController::class, 'create'])->name('rooms.create');
    Route::post('/hotels/{hotel}/rooms', [RoomController::class, 'store'])->name('rooms.store');
    Route::get('/hotels/{hotel}/rooms/{room}/edit', [RoomController::class, 'edit'])->name('rooms.edit');
    Route::put('/hotels/{hotel}/rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
    Route::delete('/hotels/{hotel}/rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');
});
