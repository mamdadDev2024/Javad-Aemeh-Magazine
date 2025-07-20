<?php

namespace App\Http\Controllers;

use App\Helpers\SweetAlert2;
use App\Models\Article;
use App\Models\Event;
use App\Models\Khabar;
use Devrabiul\ToastMagic\Facades\ToastMagic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    public function createComment(Request $request , $model , $contentId)
    {
        $data = $request->validated();
        $classModel = "App\\Models\\$model";
        $model = new $classModel;
        $model = $model->find($contentId);
        try {
            $model->comments()->create([
                "text" => $data["text"],
                "user_id" => Auth::id()
            ]);
            return ToastMagic::success("نظر شما ثبت شد", "منتظر تایید باشید" , "success");
        } catch (\Throwable $th) {
            Log::error("Error creating comment: " . $th->getMessage());
            return ToastMagic::error("مشکلی پیش آمده", "در صورتی که شرایط عادی است به ادمین گزارش دهید", "error");
        }
    }
}
