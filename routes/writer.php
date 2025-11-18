<?php

use App\Http\Controllers\programmerController;
use App\Http\Controllers\WriterController;
use Illuminate\Support\Facades\Route;



Route::middleware(['role:admin|super admin', 'auth'])
    ->as('writer.')
    ->prefix('writer-panel')
    ->group(function () {
        // Dashboard
        Route::get('/', [WriterController::class, 'index'])->name('index');

        // Article Routes
        Route::get('/magazine-create', [WriterController::class, 'magazineCreateView'])->name('magazine.create');
        Route::post('/magazine-create', [WriterController::class, 'magazineCreate'])->name('magazine.do.create');
        Route::get('/magazine-edit/{Magazine:slug}', [WriterController::class, 'MagazineUpdateView'])->name('magazine.edit');
        Route::put('/magazine-do-edit', [WriterController::class, 'MagazineUpdate'])->name('magazine.do.update');

        // Event Routes
        Route::get('/event-create', [WriterController::class, 'eventCreateView'])->name('event.create');
        Route::post('/event-create', [WriterController::class, 'eventCreate'])->name('event.do.create');
        Route::get('/event-edit/{Event:slug}', [WriterController::class, 'eventUpdateView'])->name('event.edit');
        Route::put('/event-do-edit', [WriterController::class, 'eventUpdate'])->name('event.do.update');

        // News Routes
        Route::get('/news-create', [WriterController::class, 'newsCreateView'])->name('new.create');
        Route::post('/news-create', [WriterController::class, 'newsCreate'])->name('new.do.create');
        Route::get('/new-edit/{Khabar:slug}', [WriterController::class, 'newsUpdateView'])->name('new.edit');
        Route::put('/new-do-edit', [WriterController::class, 'newsUpdate'])->name('new.do.update');
    });
