<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\ShowController;
use Illuminate\Support\Facades\Route;

// ==============================
// Public Routes
// ==============================
Route::get('/', [MainController::class, "landing"])->name("home");
Route::get('/news', [IndexController::class, "news"])->name("news");
Route::get('/events', [IndexController::class, "events"])->name("events");
Route::get('/magazines', [IndexController::class, "magazines"])->name("magazines");
// ==============================
// Content Display Routes
// ==============================
Route::get("/article/{Article:slug}" , [ShowController::class , "articleShow"])->name("Article.show");
Route::get('/magazine/{Magazine:slug}', [ShowController::class, 'magazineShow'])->name('Magazine.show');
Route::get("/event/{Event:slug}", [ShowController::class, "eventShow"])->name("Event.show");
Route::get("/news/{Khabar:slug}", [ShowController::class, "newsShow"])->name("Khabar.show");
// ==============================
// User Interaction Routes
// ==============================
Route::get("/download" , [MainController::class , "download"])->name("download");
Route::post('/create-comment/{model}/{contentId}', [CommentController::class, 'createComment'])->name('create.comment');
Route::get('/contact-us', [MainController::class, "contact"])->middleware("auth")->name("contact");
Route::post('/contact-us', [MainController::class, "doContact"])->middleware("auth")->name("do.contact");
Route::post("/like/{type}/{id}", [LikeController::class, "toggleLike"])->middleware("auth")->name("toggle.like");
Route::get("/search", [MainController::class, "search"])->name("search");
Route::post("/profile", [UserController::class, "profile"])->middleware("auth")->name("profile");

require_once __DIR__."/admin.php";
require_once __DIR__."/user.php";
require_once __DIR__."/writer.php";
require_once __DIR__."/auth.php";
