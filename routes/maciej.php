<?php

use App\Http\Controllers\HotelPhotoController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\RoomPhotoController;
use App\Models\Photo;
use App\Models\Review;
use Illuminate\Support\Facades\Route;

Route::bind('photo', fn (string $value) => Photo::query()->whereKey($value)->firstOrFail());
Route::bind('review', fn (string $value) => Review::query()->whereKey($value)->firstOrFail());

Route::get('/hotels/{hotel}/photos', [HotelPhotoController::class, 'index'])->name('hotels.photos.index');
Route::post('/hotels/{hotel}/photos', [HotelPhotoController::class, 'store'])->name('hotels.photos.store');
Route::patch('/hotels/{hotel}/photos/{photo}', [HotelPhotoController::class, 'update'])->name('hotels.photos.update');
Route::delete('/hotels/{hotel}/photos/{photo}', [HotelPhotoController::class, 'destroy'])->name('hotels.photos.destroy');

Route::get('/hotels/{hotel}/rooms/{room}/photos', [RoomPhotoController::class, 'index'])->name('rooms.photos.index');
Route::post('/hotels/{hotel}/rooms/{room}/photos', [RoomPhotoController::class, 'store'])->name('rooms.photos.store');
Route::patch('/hotels/{hotel}/rooms/{room}/photos/{photo}', [RoomPhotoController::class, 'update'])->name('rooms.photos.update');
Route::delete('/hotels/{hotel}/rooms/{room}/photos/{photo}', [RoomPhotoController::class, 'destroy'])->name('rooms.photos.destroy');

Route::get('/hotels/{hotel}/reviews/create', [ReviewController::class, 'create'])->name('hotels.reviews.create');
Route::post('/hotels/{hotel}/reviews', [ReviewController::class, 'store'])->name('hotels.reviews.store');
Route::get('/hotels/{hotel}/reviews/{review}/edit', [ReviewController::class, 'edit'])->name('hotels.reviews.edit');
Route::put('/hotels/{hotel}/reviews/{review}', [ReviewController::class, 'update'])->name('hotels.reviews.update');
Route::delete('/hotels/{hotel}/reviews/{review}', [ReviewController::class, 'destroy'])->name('hotels.reviews.destroy');
