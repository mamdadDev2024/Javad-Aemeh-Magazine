<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Devrabiul\ToastMagic\Facades\ToastMagic;

class AuthController extends Controller
{
    public function login_view()
    {
        return view("auth.login");
    }
    public function register_view()
    {
        return view("auth.register");
    }
    public function resetView()
    {
        if (session()->has('reset_user_id')) {
            return view('auth.reset');
        }
        return redirect('/');
    }
    public function forgetView()
    {
        return view('auth.forget');
    }
    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        try {
            if (Auth::attempt(['username' => $data["username"], 'password' => $data["password"]])) {
                ToastMagic::success("خوش آمدید", "ورود با موفقیت انجام شد");
                return redirect()->route('home');
            } else {
                ToastMagic::error("خطا", "حساب کاربری با این مشخصات پیدا نشد");
                return redirect()->back();
            }
        } catch (\Throwable $th) {
            Log::error("Login error => " . $th->getMessage());
            ToastMagic::error("خطا", "مشکلی در ورود به حساب ایجاد شده. لطفا دوباره تلاش کنید یا با ادمین تماس بگیرید.");
            return redirect()->back();
        }
    }
    public function forget(ResetPasswordRequest $request)
    {
        $data = $request->validated();

        $user = User::where('number', $data['number'])
                    ->where('username', $data['username'])
                    ->first();

        if ($user) {
            if (!$user->hasRole("super admin")) {
                session(['reset_user_id' => $user->id]);
                ToastMagic::alert('انجام شد!', 'اطلاعات شما تایید شد. لطفاً رمز عبور جدید خود را وارد کنید.');
            }else{
                return redirect("/");
            }
            return redirect()->route('reset');
        }

        ToastMagic::error('خطا!', 'اطلاعات وارد شده نادرست است. لطفاً دوباره تلاش کنید.');

        return redirect()->back()->withInput();
    }
    public function reset(Request $request)
    {
        $data = $request->validated();
        if (!session()->has('reset_user_id')) {
            ToastMagic::alert('خطا!', 'شما دسترسی به این مسیر ندارید!');
            return redirect()->route('home');
        }

        $user = User::find(session('reset_user_id'));

        if (!$user) {
            ToastMagic::alert('خطا!', 'کاربر مورد نظر یافت نشد!');
            return redirect()->route('home');
        }

        $user->update(['password' => Hash::make($data['password'])]);

        session()->forget('reset_user_id');
        ToastMagic::alert('انجام شد!', 'رمز عبور شما با موفقیت تغییر کرد!');
        return redirect()->route('home');
    }
    public function register(Request $request)
    {
        $data = $request->validate();

        try {
            $user = User::create([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                "number" => $data["number"]
            ]);

            $user->assignRole("user");
            Auth::login($user);
            return ToastMagic::success("خوش آمدید", "ثبت‌نام با موفقیت انجام شد");
        } catch (\Throwable $th) {
            Log::error("Error in registration at " . now() . " => " . $th->getMessage());
            ToastMagic::alert("خطا", "مشکلی در ثبت‌نام رخ داده است.");

            return redirect()->back();
        }
    }
    public function logout()
    {
        Auth::logout();
        return ToastMagic::success("خروج", "با موفقیت از حساب خود خارج شدید");
    }
}
