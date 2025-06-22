<?php

namespace App\Http\Controllers;

use App\Helpers\SweetAlert2;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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
    public function login(Request $request)
    {
        $data = $request->validate([
            'g-recaptcha-response' => 'required|captcha',
            "username" => "required|min:4|max:60",
            "password" => "required"
        ], [
            'g-recaptcha-response.required' => 'لطفاً تأیید کنید که شما ربات نیستید.',
            'g-recaptcha-response.captcha' => 'اعتبارسنجی reCAPTCHA ناموفق بود. لطفاً دوباره امتحان کنید.',]
        );
        try {
            if (Auth::attempt(['username' => $data["username"], 'password' => $data["password"]])) {
                return $this->setFlashMessage("خوش آمدید", "ورود با موفقیت انجام شد");
            } else {
                session()->flash("alert" ,SweetAlert2::alert("خطا", "حساب کاربری با این مشخصات پیدا نشد", "error"));
                return redirect()->back();
            }
        } catch (\Throwable $th) {
            Log::error("Login error => " . $th->getMessage());
            session()->flash("alert" ,SweetAlert2::alert("خطا", "مشکلی در ورود به حساب ایجاد شده. لطفا دوباره تلاش کنید یا با ادمین تماس بگیرید.", "error"));
            return redirect()->back();
        }
    }

    public function forgetView()
    {
        return view('auth.forget');
    }

    public function forget(Request $request)
    {
        $data = $request->validate([
            'number' => 'required|numeric|exists:users,number',
            'username' => 'required|string|exists:users,username',
        ]);

        $user = User::where('number', $data['number'])
                    ->where('username', $data['username'])
                    ->first();

        if ($user) {
            if (!$user->hasRole("super admin")) {
                session(['reset_user_id' => $user->id]);
                session()->flash(
                    'alert',
                    SweetAlert2::alert('انجام شد!', 'اطلاعات شما تایید شد. لطفاً رمز عبور جدید خود را وارد کنید.'));
            }else{
                return redirect("/");
            }
            return redirect()->route('reset');
        }

        session()->flash(
            'alert',
            SweetAlert2::alert('خطا!', 'اطلاعات وارد شده نادرست است. لطفاً دوباره تلاش کنید.')
        );

        return redirect()->back()->withInput();
    }


    public function resetView()
    {
        if (session()->has('reset_user_id')) {
            return view('auth.reset');
        }
        return redirect('/');
    }

    public function reset(Request $request)
    {
        $data = $request->validate([
            "g-recaptcha-response" => "required|captcha",
            'password' => 'required|string|min:6|max:50|confirmed',
        ]);
        if (!session()->has('reset_user_id')) {
            session()->flash('alert', SweetAlert2::alert('خطا!', 'شما دسترسی به این مسیر ندارید!'));
            return redirect()->route('home');
        }

        $user = User::find(session('reset_user_id'));

        if (!$user) {
            session()->flash('alert', SweetAlert2::alert('خطا!', 'کاربر مورد نظر یافت نشد!'));
            return redirect()->route('home');
        }

        $user->update(['password' => Hash::make($data['password'])]);

        session()->forget('reset_user_id');
        session()->flash('alert', SweetAlert2::alert('انجام شد!', 'رمز عبور شما با موفقیت تغییر کرد!'));
        return redirect()->route('home');
    }


    public function register(Request $request)
    {
        $data = $request->validate([
            'g-recaptcha-response' => 'required|captcha',
            "username" => "required|unique:users,username|min:4|max:60",
            "email" => "required|email|unique:users,email",
            "password" => "required|min:6|max:50|string|confirmed",
            "number" => "required|numeric|unique:users,number"
        ], [
            'g-recaptcha-response.required' => 'لطفاً تأیید کنید که شما ربات نیستید.',
            'g-recaptcha-response.captcha' => 'اعتبارسنجی reCAPTCHA ناموفق بود. لطفاً دوباره امتحان کنید.',
        ]);

        try {
            $user = User::create([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                "number" => $data["number"]
            ]);

            $user->assignRole("user");
            Auth::login($user);
            return $this->setFlashMessage("خوش آمدید", "ثبت‌نام با موفقیت انجام شد");
        } catch (\Throwable $th) {
            Log::error("Error in registration at " . now() . " => " . $th->getMessage());
            session()->flash("alert" , SweetAlert2::alert("خطا", "مشکلی در ثبت‌نام رخ داده است.", "error"));

            return redirect()->back();
        }
    }
    public function logout()
    {
        Auth::logout();
        return $this->setFlashMessage("خروج", "با موفقیت از حساب خود خارج شدید");
    }
    private function setFlashMessage($title, $message, $type = 'success')
    {
        session()->flash("alert", SweetAlert2::alert($title, $message, $type));
        return redirect()->intended('/');
    }
}
