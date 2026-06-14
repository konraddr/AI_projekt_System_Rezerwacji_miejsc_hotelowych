<?php

use App\Http\Controllers\AdminHotelController;
use App\Http\Controllers\AdminPanelController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AdminReviewController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\HotelPhotoController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\RoomPhotoController;
use App\Models\Photo;
use App\Models\Report;
use App\Models\Review;
use Illuminate\Support\Facades\Route;

Route::bind('photo', fn (string $value) => Photo::query()->whereKey($value)->firstOrFail());
Route::bind('review', fn (string $value) => Review::query()->whereKey($value)->firstOrFail());
Route::bind('report', fn (string $value) => Report::query()->whereKey($value)->firstOrFail());

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

Route::get('/hotels/{hotel}/chat', [MessageController::class, 'chat'])->name('hotels.chat');
Route::get('/hotels/{hotel}/messages', [MessageController::class, 'index'])->name('hotels.messages.index');
Route::post('/hotels/{hotel}/messages', [MessageController::class, 'store'])->name('hotels.messages.store');

Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
Route::patch('/reports/{report}/status', [ReportController::class, 'updateStatus'])->name('reports.update-status');

Route::middleware('permission:0')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminPanelController::class, 'index'])->name('index');

    Route::get('/hotels', [AdminHotelController::class, 'index'])->name('hotels.index');
    Route::get('/hotels/create', [AdminHotelController::class, 'create'])->name('hotels.create');
    Route::post('/hotels', [AdminHotelController::class, 'store'])->name('hotels.store');
    Route::get('/hotels/{hotel}', [AdminHotelController::class, 'show'])->name('hotels.show');
    Route::get('/hotels/{hotel}/edit', [AdminHotelController::class, 'edit'])->name('hotels.edit');
    Route::put('/hotels/{hotel}', [AdminHotelController::class, 'update'])->name('hotels.update');
    Route::delete('/hotels/{hotel}', [AdminHotelController::class, 'destroy'])->name('hotels.destroy');

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::patch('/reviews/{review}/ban', [AdminReviewController::class, 'ban'])->name('reviews.ban');
    Route::patch('/reviews/{review}/unban', [AdminReviewController::class, 'unban'])->name('reviews.unban');

    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::patch('/reports/{report}/status', [AdminReportController::class, 'updateStatus'])->name('reports.update-status');
});
