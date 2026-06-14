<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\DataNewsController;
use App\Http\Controllers\DataPagesController;
use App\Http\Controllers\DataImagesController;
use App\Http\Controllers\DataPartsController;
use App\Http\Controllers\DataLogsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('kriteria', KriteriaController::class);
    Route::resource('data-news', DataNewsController::class);
    Route::resource('data-pages', DataPagesController::class);
    Route::get('data-images/import', [DataImagesController::class, 'import'])->name('data-images.import');
    Route::post('data-images/import-process', [DataImagesController::class, 'importProcess'])->name('data-images.import-process');
    Route::resource('data-images', DataImagesController::class);
    Route::resource('data-parts', DataPartsController::class);
    Route::resource('data-logs', DataLogsController::class);
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
