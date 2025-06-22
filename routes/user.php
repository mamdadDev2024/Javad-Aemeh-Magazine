<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;



Route::prefix("user-panel")
    ->as("user.")
    ->middleware("auth")
    ->group(function () {
        Route::get("/", [UserController::class, "profileView"])->name("profile");
        Route::get("/change-password", [UserController::class, "changePassword"])->name("change.password");
        Route::post("/change-password", [UserController::class, "doChangePassword"])->name("do.change.password");
        Route::get("/create", [UserController::class, "create"])->name("create");
        Route::post("/do_create", [UserController::class, "doSuggest"])->name("do.suggest");
        Route::get("/delete/{id}", [UserController::class, "destroy"])->name("delete");
    });
