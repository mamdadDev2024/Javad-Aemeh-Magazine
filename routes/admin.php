<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ShowController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin-panel')
    ->as('admin.')
    ->middleware('auth')
    ->group(function () {
        Route::middleware('role:super admin')->group(function () {
            Route::get('/', [AdminController::class, 'panel'])->name('panel');
            Route::get('/users', [AdminController::class, 'indexUsers'])->name('index_users');
            Route::delete('/user-delete/{id}', [AdminController::class, 'userDestroy'])->name('user.destroy');
            Route::post('/update-user', [AdminController::class, 'updateUsers'])->name('update.users');
            Route::post('/section-update/{name}', [\App\Http\Controllers\SectionController::class, 'editSection'])->name('update');
            Route::post('/update-all', [AdminController::class, 'updateAll'])->name('update.all');
            Route::post('/create-link', [AdminController::class, 'createLink'])->name('create.link');
            Route::post('/delete-links', [AdminController::class, 'deleteLink'])->name('delete.link');
        });

        Route::middleware('role:admin|super admin')->group(function () {
            Route::get('/comments', [AdminController::class, 'indexComments'])->name('index_comments');
            Route::get('/contacts', [AdminController::class, 'indexContacts'])->name('index_contacts');
            Route::get('/contents', [AdminController::class, 'indexContents'])->name('index_contents');
            Route::get('/reports', [AdminController::class, 'indexReports'])->name('index_reports');

            // Contact Management
            Route::delete('/delete-contact/{id}', [AdminController::class, 'contactDelete'])->name('contact.delete');

            // Comment Management
            Route::post('/accept-comment/{id}', [AdminController::class, 'acceptComment'])->name('comment.accept');
            Route::delete('/delete-comment/{id}', [AdminController::class, 'deleteComment'])->name('comment.delete');
            Route::get('/accept-all-comment', [AdminController::class, 'approveAllComments'])->name('comment.accept.all');

            // Post Management
            Route::delete('/delete-magazine/{id}', [AdminController::class, 'deleteMagazine'])->name('magazine.delete');
            Route::delete('/delete-recommend/{id}', [AdminController::class, 'deleteRecommend'])->name('recommend.delete');
            Route::post('/accept-new/{id}', [AdminController::class, 'acceptNews'])->name('new.accept');
            Route::delete('/delete-new/{id}', [AdminController::class, 'deleteNew'])->name('new.delete');
            Route::post('/accept-event/{id}', [AdminController::class, 'acceptEvent'])->name('event.accept');
            Route::delete('/delete-event/{id}', [AdminController::class, 'deleteEvent'])->name('event.delete');
            Route::get('/accept-all-post', [AdminController::class, 'approveAllPosts'])->name('post.accept.all');

            // Preview Routes
            Route::get('/article_preview/{id}', [ShowController::class, 'articlePreview'])->name('article.preview');
            Route::get('/event_preview/{id}', [ShowController::class, 'eventPreview'])->name('event.preview');
            Route::get('/news_preview/{id}', [ShowController::class, 'newsPreview'])->name('new.preview');
        });
    });
