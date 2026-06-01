<?php

use App\Http\Controllers\HotelPhotoController;
use App\Http\Controllers\RoomPhotoController;
use App\Models\Photo;
use Illuminate\Support\Facades\Route;

Route::bind('photo', fn (string $value) => Photo::query()->whereKey($value)->firstOrFail());

Route::get('/hotels/{hotel}/photos', [HotelPhotoController::class, 'index'])->name('hotels.photos.index');
Route::post('/hotels/{hotel}/photos', [HotelPhotoController::class, 'store'])->name('hotels.photos.store');
Route::patch('/hotels/{hotel}/photos/{photo}', [HotelPhotoController::class, 'update'])->name('hotels.photos.update');
Route::delete('/hotels/{hotel}/photos/{photo}', [HotelPhotoController::class, 'destroy'])->name('hotels.photos.destroy');

Route::get('/hotels/{hotel}/rooms/{room}/photos', [RoomPhotoController::class, 'index'])->name('rooms.photos.index');
Route::post('/hotels/{hotel}/rooms/{room}/photos', [RoomPhotoController::class, 'store'])->name('rooms.photos.store');
Route::patch('/hotels/{hotel}/rooms/{room}/photos/{photo}', [RoomPhotoController::class, 'update'])->name('rooms.photos.update');
Route::delete('/hotels/{hotel}/rooms/{room}/photos/{photo}', [RoomPhotoController::class, 'destroy'])->name('rooms.photos.destroy');
