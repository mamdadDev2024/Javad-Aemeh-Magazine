<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Devrabiul\ToastMagic\Facades\ToastMagic;

class UserController extends Controller
{
    public function changePassword()
    {
        $user_id = Auth::id();
        return view("user-panel.change_password", compact("user_id"));
    }

    public function doChangePassword(ChangePasswordRequest $request)
    {
        $data = $request->validated();

        $user = Auth::user();
        if ($user->id !== (int)$data['id']) {
            ToastMagic::error("خطا", "مشکلی رخ داده. لطفا بعدا تلاش بفرمایید", "error");
            return redirect()->back();
        }

        $user->password = Hash::make($data["password"]);
        $user->save();

        ToastMagic::success("انجام شد", "رمز عبور شما با موفقیت تغییر کرد");
        return redirect()->back();
    }

    public function profileView()
    {
        $user = Auth::user();
        return view("user-panel.profile", compact("user"));
    }

    public function profile(UpdateUserRequest $request)
    {
        $data = $request->validated();

        if ($data['delete'] && Auth::check()) {
            // CHANGED: Deleting account should be DELETE with CSRF; redirect to proper method
            return $this->destroy(Auth::id());
        }


        $user = Auth::user();

        if ($request->hasFile("image")) {
            $this->handleImageUpload($request, $user);
        }

        $this->updateUserFields($user, $data);

        if ($user->save()) {
            ToastMagic::success("انجام شد!", "اطلاعات کاربری شما با موفقیت ویرایش شد");
        } else {
            ToastMagic::error("انجام نشد!", "به‌روزرسانی اطلاعات کاربری با خطا مواجه شد", "error");
        }

        return back();
    }

    private function handleImageUpload(Request $request, $user)
    {
        try {
            $file = $request->file('image');
            $path = $file->store("images", "public");
            $user->image = $path;
        } catch (\Throwable $e) {
            Log::error("Image upload error: " . $e->getMessage());
            ToastMagic::error("خطا در بارگذاری عکس", "احتمالاً مشکل از حجم یا فرمت فایل است", "error");
        }
    }

    private function updateUserFields($user, array $data)
    {
        foreach (['email', 'name' , 'number', 'age'] as $field) {
            if (array_key_exists($field, $data) && $data[$field] !== null) {
                $user->{$field} = $data[$field];
            }
        }
    }

    public function create()
    {
        return view("user-panel.suggest");
    }

    public function doSuggest(Request $request)
    {
        // CHANGED: Add proper validation for suggest
        $data = $request->validate([
            'g-recaptcha-response' => 'required|captcha',
            'title' => 'required|string|min:6|max:150',
            'pdf' => 'required|file|mimes:pdf|max:10240',
            'word' => 'nullable|file|mimes:doc,docx|max:10240',
        ], [
            'g-recaptcha-response.required' => 'لطفاً تأیید کنید که شما ربات نیستید.',
            'g-recaptcha-response.captcha' => 'اعتبارسنجی reCAPTCHA ناموفق بود. لطفاً دوباره امتحان کنید.',
        ]);

        try {
            $user = Auth::user();
            $pdfPath = $request->file("pdf")->store("attachments", "public");
            $wordPath = $request->hasFile("word") ? $request->file("word")->store("attachments", "public") : null;

            $user->recommends()->create([
                "pdf" => $pdfPath,
                "word" => $wordPath,
                "slug" => Str::slug($data["title"]),
                "title" => $data["title"],
            ]);

            ToastMagic::success('ثبت موفق', 'پیشنهاد شما ثبت شد و پس از تأیید منتشر می‌شود');
        } catch (\Throwable $e) {
            Log::error("Suggest error: " . $e->getMessage());
            ToastMagic::error("خطا", "پیشنهاد شما ثبت نشد. لطفاً بعداً تلاش کنید", "error");
        }

        return back();
    }

    public function destroy(string $id)
    {
        $user = User::find($id);

        if (!$user || Auth::id() !== $user->id) {
            ToastMagic::error("خطا", "شما مجاز به حذف این کاربر نیستید", "error");
            return back();
        }

        if ($user->hasRole("super admin")) {
            ToastMagic::error("ممنوع", "شما نمی‌توانید ادمین سوپر را حذف کنید", "error");
            return back();
        }

        if ($user->delete()) {
            ToastMagic::success("موفق", "کاربر با موفقیت حذف شد");
        } else {
            ToastMagic::error("خطا", "حذف کاربر انجام نشد", "error");
        }

        return back();
    }
}
