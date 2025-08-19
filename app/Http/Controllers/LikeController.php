<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Event;
use App\Models\Khabar;
use App\Models\Magazine;
use Devrabiul\ToastMagic\Facades\ToastMagic;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LikeController extends Controller
{
    public function toggleLike(string $type, int $id)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                ToastMagic::success("احراز هویت", "اول وارد حساب شوید");
                return back();
            }

            $model = match ($type) {
                "Magazine" => Magazine::class,
                'article' => Article::class,
                'khabar' => Khabar::class,
                'event' => Event::class,
                default => null
            };

            if (!$model) {
                ToastMagic::error("خطا", "نوع محتوا نامعتبر است");
                return back();
            }

            $content = $model::findOrFail($id);
            $likeToggled = $user->toggleLike($content);

            $message = $likeToggled
                ? "با موفقیت انجام شد"
                : "لایک شما حذف شد";

            ToastMagic::success("انجام شد", $message);
            return back();

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            ToastMagic::error("خطا", "محتوای مورد نظر یافت نشد");
            return back();

        } catch (\Throwable $th) {
            Log::error("Error in toggleLike for {$type}: " . $th->getMessage());
            ToastMagic::error("خطا", "مشکلی در پردازش درخواست شما رخ داد");
            return back();

        }
    }
}
