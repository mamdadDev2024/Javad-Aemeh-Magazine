<?php

namespace App\Http\Controllers;

use App\Helpers\SweetAlert2;
use App\Models\Article;
use App\Models\Event;
use App\Models\Khabar;
use App\Models\Magazine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LikeController extends Controller
{
    public function toggleLike(string $type, int $id)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return $this->flashAlertAndRedirect("احراز هویت", "اول وارد حساب شوید", "error");
            }

            $model = match ($type) {
                "Magazine" => Magazine::class,
                'article' => Article::class,
                'khabar' => Khabar::class,
                'event' => Event::class,
                default => null
            };

            if (!$model) {
                return $this->flashAlertAndRedirect("خطا", "نوع محتوا نامعتبر است", "error");
            }

            $content = $model::findOrFail($id);
            $likeToggled = $user->toggleLike($content);

            $message = $likeToggled
                ? "با موفقیت انجام شد"
                : "لایک شما حذف شد";

            return $this->flashAlertAndRedirect("انجام شد", $message , "success");

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->flashAlertAndRedirect("خطا", "محتوای مورد نظر یافت نشد", "error");

        } catch (\Throwable $th) {
            Log::error("Error in toggleLike for {$type}: " . $th->getMessage());
            return $this->flashAlertAndRedirect("خطا", "مشکلی در پردازش درخواست شما رخ داد", "error");
        }
    }
    private function flashAlertAndRedirect(string $title, string $message, string $type = null)
    {
        session()->flash("alert", SweetAlert2::alert($title, $message, $type));
        return back();
    }
}
