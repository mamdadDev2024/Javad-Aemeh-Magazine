<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::prefix('auth')
    ->middleware(['guest'])
    ->group(function () {
        Route::get('/login', [AuthController::class, "login_view"])->name('login');
        Route::get('/register', [AuthController::class, 'register_view'])->name('register');
        Route::post('/do_login', [AuthController::class, "login"])->name('do_login');
        Route::post('/do_register', [AuthController::class, 'register'])->name('do_register');
        Route::get("/forget" , [AuthController::class , "forgetView"])->name("forget");
        Route::post("/forget" , [AuthController::class , "forget"])->name("do_forget");
        Route::get("/reset" , [AuthController::class , "resetView"])->name("reset");
        Route::post("/reset" , [AuthController::class , "reset"])->name("do_reset");
    });
Route::get('/logout', [AuthController::class, 'logout'])->name('auth.logout')->middleware('auth');
