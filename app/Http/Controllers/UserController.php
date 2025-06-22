<?php

namespace App\Http\Controllers;

use App\Helpers\SweetAlert2;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class UserController extends Controller
{
    public function changePassword(Request $request){
        $user_id = Auth::id();
        return view("user-panel.change_password" , compact("user_id"));
    }
    public function doChangePassword(Request $request){
        $data = $request->validate([
            "id" => "required|numeric",
            "password" => "required|min:6|max:40|confirmed"
        ]);
        $user = Auth::id();
        if (!($user->id == $data["id"])){
            session()->flash("alert" , SweetAlert2::alert("خطا" , "مشکلی رخ داده. لطفا بعدا تلاش بفرمایید" , "error"));
            return redirect()->back();
        }else{
            $user->password = Hash::make($data["password"]);
            $user->save();
            session()->flash("alert" , SweetAlert2::alert("انجام شد" , "رمز عبور شما با موفقیت تغییر کرد"));
            return redirect()->back();
        }
    }
    public function profileView()
    {
        $user = Auth::user()->toArray();
        return view("user-panel.profile", compact("user"));
    }

    public function profile(Request $request)
    {
        if (Storage::exists("settings.json")) {
            $setting = Storage::get("settings.json");
            $setting = json_decode($setting, true);

            if (!empty($request->activate) && Auth::check()) {
                $setting["activate"] = false;
                Storage::put("settings.json", json_encode($setting, JSON_PRETTY_PRINT));
            }else{
                $setting["activate"] = true;
                Storage::put("settings.json", json_encode($setting, JSON_PRETTY_PRINT));
            }
        } else {
            Storage::put("settings.json", json_encode(["activate" => true], JSON_PRETTY_PRINT));
        }
        if ($request->delete && Auth::check()) {
            return redirect()->route("user.delete", Auth::id());
        }
        $data = $request->validate([
            // 'g-recaptcha-response' => 'required|captcha',
            "email" => "nullable|email|unique:users,email," . Auth::id(),
            "age" => "nullable|numeric",
            "name" => "nullable|min:4|max:40",
            "number" => "nullable|numeric|min:9|unique:users,number",
            "image" => "nullable|image|mimes:jpg,png|max:5120"
        ]);

        $user = Auth::user();
        if ($request->has('image') && $request->file("image") !== $user->image) {
            $this->handleImageUpload($request, $user);
        }
        $this->updateUserFields($request, $user, $data);
        if ($user->save()) {
            session()->flash("alert", SweetAlert2::alert("انجام شد!", "اطلاعات کاربری شما با موفقیت ویرایش شد"));
            return back();
        } else {
            session()->flash("alert", SweetAlert2::alert("انجام نشد!", "به روز رسانی اطلاعات کاربری شما با شکست مواجه شد", "error"));
            return back();
        }
    }

    private function handleImageUpload($request, $user)
    {
        try {
            $file = $request->file('image');
            $path = $file->store("images", "public");
            $user->image = $path;
        } catch (\Throwable $th) {
            Log::error("at image change in profile => " . $th);
            session()->flash("alert", SweetAlert2::alert("در بارگذاری عکس مشکلی به وجود آمد!", "احتمالا مشکل از حجم عکس بود", "error"));
            return back();
        }
    }

    private function updateUserFields($request, $user, $data)
    {
        if ($request->has('email') && $user->email !== $data["email"]) {
            $user->email = $data["email"];
        }

        if ($request->has('name')) {
            $user->name = $data["name"];
        }

        if ($request->has('number')) {
            $user->number = $data["number"];
        }
    }

    public function create()
    {
        return view("user-panel.suggest");
    }

    public function doSuggest(Request $request)
    {
            $data = $request->validate([
                "g-recaptcha-response"=>"required|captcha",
                "pdf" => "required|file|mimes:pdf|max:8000",
                "word" => "nullable|file|mimes:docx|max:8000",
                "title" => "required|min:6|max:100",
            ], [
                'g-recaptcha-response.required' => 'لطفاً تأیید کنید که شما ربات نیستید.',
                'g-recaptcha-response.captcha' => 'اعتبارسنجی reCAPTCHA ناموفق بود. لطفاً دوباره امتحان کنید.',
            ]);


        try {
            $user = Auth::user();
            $pdfPath = $request->file("pdf")->store("attachments", "public");
            $wordPath = $request->hasFile("word") ?$request->file("word")->store("attachments", "public") : "";

            $user->recommends()->create([
                "pdf" => $pdfPath,
                "word" => $wordPath,
                "slug" => Str::slug($data["title"]),
                "title" => $data["title"],
            ]);

            session()->flash('alert', SweetAlert2::alert('پیشنهاد شما ثبت شد', 'پس از تایید در قسمت نشریه ها منتشر می شود'));
            return redirect()->back();
        } catch (\Throwable $th) {
            Log::error("at create suggest => " . $th->getMessage());
            session()->flash('alert', SweetAlert2::alert("مشکلی به وجود آمد", "پیشنهاد شما ثبت نشد در صورت نیاز بعدا تلاش و یا با ادمین در میان بگذارید", "error"));
            return redirect()->back();
        }
    }


    public function destroy(string $id)
    {
        $user = \App\Models\User::find($id);
        if (Auth::id() == $user->id) {
            if ($user->hasRole("super admin")) {
                session()->flash("alert", SweetAlert2::alert("حذف نشد!", "شما مجاز به حذف ادمین سوپر نیستید", "error"));
                return redirect()->back();
            }

            if ($user->delete()) {
                session()->flash("alert", SweetAlert2::alert("حذف شد!", "کاربر با موفقیت حذف شد"));
                return redirect()->back();
            } else {
                session()->flash("alert", SweetAlert2::alert("حذف نشد!", "عملیات حذف کاربر با خطا مواجه شد", "error"));
                return redirect()->back();
            }
        } else {
            session()->flash("alert", SweetAlert2::alert("حذف نشد!", "شما مجاز به حذف این کاربر نیستید", "error"));
            return redirect()->back();
        }
    }
}
