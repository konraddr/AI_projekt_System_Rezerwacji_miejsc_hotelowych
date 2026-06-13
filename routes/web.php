<?php

use App\Http\Controllers\AmenityController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PhotoFileController;
use App\Http\Controllers\HotelBookingController;
use App\Http\Controllers\ReviewReportController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\HotelWorkerController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/hotels', [HotelController::class, 'index'])->name('hotels.index');
Route::get('/hotels/{hotel}', [HotelController::class, 'show'])->name('hotels.show');
Route::get('/photos/{photo}', [PhotoFileController::class, 'show'])->name('photos.show');
Route::post('/hotels/{hotel}/reviews/{review}/report', [ReviewReportController::class, 'store'])->name('hotels.reviews.report');

Route::middleware('auth')->prefix('bookings')->name('bookings.')->group(function () {
    Route::get('/', [BookingController::class, 'index'])->name('index');
    Route::get('/hotels/{hotel}/rooms/{room}/create', [BookingController::class, 'create'])->name('create');
    Route::post('/hotels/{hotel}/rooms/{room}', [BookingController::class, 'store'])->name('store');
    Route::post('/{booking}/pay', [BookingController::class, 'pay'])->name('pay');
    Route::post('/{booking}/fail-payment', [BookingController::class, 'failPayment'])->name('fail-payment');
    Route::post('/{booking}/cancel', [BookingController::class, 'cancel'])->name('cancel');
    Route::get('/{booking}', [BookingController::class, 'show'])->name('show');
});

Route::middleware('auth')->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
    Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
    Route::post('/{notification}', [NotificationController::class, 'markAsRead'])->name('read');
});

Route::middleware('auth')->prefix('push-subscriptions')->name('push-subscriptions.')->group(function () {
    Route::post('/', [PushSubscriptionController::class, 'store'])->name('store');
    Route::delete('/', [PushSubscriptionController::class, 'destroy'])->name('destroy');
});

Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'edit'])->name('edit');
    Route::put('/', [ProfileController::class, 'update'])->name('update');
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

    Route::get('/hotels/{hotel}/bookings', [HotelBookingController::class, 'index'])->name('hotels.bookings.index');
    Route::get('/hotels/{hotel}/bookings/{booking}', [HotelBookingController::class, 'show'])->name('hotels.bookings.show');

    Route::get('/hotels/{hotel}/workers', [HotelWorkerController::class, 'index'])->name('hotels.workers.index');
    Route::post('/hotels/{hotel}/workers', [HotelWorkerController::class, 'store'])->name('hotels.workers.store');
    Route::put('/hotels/{hotel}/workers/{user}', [HotelWorkerController::class, 'update'])->name('hotels.workers.update');
    Route::delete('/hotels/{hotel}/workers/{user}', [HotelWorkerController::class, 'destroy'])->name('hotels.workers.destroy');

    Route::middleware('permission:0')->group(function () {
        Route::get('/amenities', [AmenityController::class, 'index'])->name('amenities.index');
        Route::get('/amenities/create', [AmenityController::class, 'create'])->name('amenities.create');
        Route::post('/amenities', [AmenityController::class, 'store'])->name('amenities.store');
        Route::get('/amenities/{amenity}/edit', [AmenityController::class, 'edit'])->name('amenities.edit');
        Route::put('/amenities/{amenity}', [AmenityController::class, 'update'])->name('amenities.update');
        Route::delete('/amenities/{amenity}', [AmenityController::class, 'destroy'])->name('amenities.destroy');
    });
});
