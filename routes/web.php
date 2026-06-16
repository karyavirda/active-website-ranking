<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CriteriasController;
use App\Http\Controllers\SubdomainsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataNewsController;
use App\Http\Controllers\DataPagesController;
use App\Http\Controllers\DataImagesController;
use App\Http\Controllers\DataPartsController;
use App\Http\Controllers\DataLogsController;
use App\Http\Controllers\SawController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('criterias', CriteriasController::class);
    Route::get('subdomains/import', [SubdomainsController::class, 'import'])->name('subdomains.import');
    Route::post('subdomains/import-process', [SubdomainsController::class, 'importProcess'])->name('subdomains.import-process');
    Route::resource('subdomains', SubdomainsController::class);
    Route::get('data-news/import', [DataNewsController::class, 'import'])->name('data-news.import');
    Route::post('data-news/import-process', [DataNewsController::class, 'importProcess'])->name('data-news.import-process');
    Route::resource('data-news', DataNewsController::class);
    Route::get('data-pages/import', [DataPagesController::class, 'import'])->name('data-pages.import');
    Route::post('data-pages/import-process', [DataPagesController::class, 'importProcess'])->name('data-pages.import-process');
    Route::resource('data-pages', DataPagesController::class);
    Route::get('data-images/import', [DataImagesController::class, 'import'])->name('data-images.import');
    Route::post('data-images/import-process', [DataImagesController::class, 'importProcess'])->name('data-images.import-process');
    Route::resource('data-images', DataImagesController::class);
    Route::get('data-parts/import', [DataPartsController::class, 'import'])->name('data-parts.import');
    Route::post('data-parts/import-process', [DataPartsController::class, 'importProcess'])->name('data-parts.import-process');
    Route::resource('data-parts', DataPartsController::class);
    Route::get('data-logs/import', [DataLogsController::class, 'import'])->name('data-logs.import');
    Route::post('data-logs/import-process', [DataLogsController::class, 'importProcess'])->name('data-logs.import-process');
    Route::resource('data-logs', DataLogsController::class);
    Route::get('saw', [SawController::class, 'index'])->middleware(['auth'])->name('saw.index');
    Route::post('/saw/process', [SawController::class, 'process'])->name('saw.process');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
