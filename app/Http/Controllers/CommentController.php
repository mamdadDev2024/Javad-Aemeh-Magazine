<?php

namespace App\Http\Controllers;

use App\Helpers\SweetAlert2;
use App\Http\Requests\Comment\CreateCommentRequest;
use App\Models\Article;
use App\Models\Event;
use App\Models\Khabar;
use Devrabiul\ToastMagic\Facades\ToastMagic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    public function __invoke(CreateCommentRequest $request , $model , $contentId)
    {
        $data = $request->validated();
        $classModel = "App\\Models\\$model";
        $model = new $classModel;
        $model = $model->find($contentId);
        try {
            $model->comments()->create([
                // CHANGED: use body (aligned with migration and request)
                "body" => $data["body"],
                "user_id" => Auth::id()
            ]);
            ToastMagic::success("نظر شما ثبت شد", "منتظر تایید باشید");
            return back();
        } catch (\Throwable $th) {
            Log::error("Error creating comment: " . $th->getMessage());
            ToastMagic::error("مشکلی پیش آمده", "در صورتی که شرایط عادی است به ادمین گزارش دهید");
            return back();
        }
    }
}
