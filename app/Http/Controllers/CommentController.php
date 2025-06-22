<?php

namespace App\Http\Controllers;

use App\Helpers\SweetAlert2;
use App\Models\Article;
use App\Models\Event;
use App\Models\Khabar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    private function setFlashMessage($title, $message, $type = 'info')
    {
        session()->flash("alert", SweetAlert2::alert($title, $message, $type));
        return redirect()->back();
    }
    public function createComment(Request $request , $model , $contentId)
    {
        $data = $request->validate([
        'g-recaptcha-response' => 'required|captcha',
            "text" => "required|string|min:5|max:1000",
        ], [
            'g-recaptcha-response.required' => 'لطفاً تأیید کنید که شما ربات نیستید.',
            'g-recaptcha-response.captcha' => 'اعتبارسنجی reCAPTCHA ناموفق بود. لطفاً دوباره امتحان کنید.',
        ]);
        $classModel = "App\\Models\\$model";
        $model = new $classModel;
        $model = $model->find($contentId);
        try {
            $model->comments()->create([
                "text" => $data["text"],
                "user_id" => Auth::id()
            ]);
            return $this->setFlashMessage("نظر شما ثبت شد", "منتظر تایید باشید" , "success");
        } catch (\Throwable $th) {
            Log::error("Error creating comment: " . $th->getMessage());
            return $this->setFlashMessage("مشکلی پیش آمده", "در صورتی که شرایط عادی است به ادمین گزارش دهید", "error");
        }
    }
}
