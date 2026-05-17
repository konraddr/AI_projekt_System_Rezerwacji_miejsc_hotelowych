<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\RoomController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/hotels', [HotelController::class, 'index'])->name('hotels.index');

//
Route::get('/hotels', [HotelController::class, 'index'])->name('hotels.index');

Route::get('/hotels/create', [HotelController::class, 'create'])->name('hotels.create');
Route::post('/hotels', [HotelController::class, 'store'])->name('hotels.store');

Route::get('/hotels/{hotel}', [HotelController::class, 'show'])->name('hotels.show');
//

//
Route::get('/hotels/{hotel}/rooms/create', [RoomController::class, 'create'])->name('rooms.create');

Route::post('/hotels/{hotel}/rooms', [RoomController::class, 'store'])->name('rooms.store');
///
