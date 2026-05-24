<?php

use App\Http\Controllers\HotelPhotoController;
use Illuminate\Support\Facades\Route;

Route::get('/hotels/{hotel}/photos', [HotelPhotoController::class, 'index'])->name('hotels.photos.index');
Route::post('/hotels/{hotel}/photos', [HotelPhotoController::class, 'store'])->name('hotels.photos.store');
Route::patch('/hotels/{hotel}/photos/{photo}', [HotelPhotoController::class, 'update'])->name('hotels.photos.update');
Route::delete('/hotels/{hotel}/photos/{photo}', [HotelPhotoController::class, 'destroy'])->name('hotels.photos.destroy');
