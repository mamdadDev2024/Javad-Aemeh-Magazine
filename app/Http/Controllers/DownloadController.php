<?php

namespace App\Http\Controllers;

use App\Http\Requests\DownloadRequest;
use Devrabiul\ToastMagic\Facades\ToastMagic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    public function __invoke(DownloadRequest $request)
    {
        $path = $request->validated()['url'];

        try {
            if ($path) {
                return Storage::exists($path) ?: Storage::download($path);
            }

            ToastMagic::error("انجام نشد", "فایل موجود نیست");
            return back();

        } catch (\Exception $e) {
            Log::error('Download error: ' . $e->getMessage());
            ToastMagic::error("انجام نشد", "مشکلی در فرآیند بارگیری پیش آمد");
            return back();
        }
    }
}
