<?php

namespace App\Http\Controllers;

use App\Http\Requests\DownloadRequest;
use Devrabiul\ToastMagic\Facades\ToastMagic;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    public function __invoke(DownloadRequest $request)
    {
        $path = $request->validated()['url'];

        try {
            if ($path) {
                // CHANGED: Check on public disk and download if exists
                if (Storage::disk('public')->exists($path)) {
                    return Storage::disk('public')->download($path);
                }
            }

            ToastMagic::error('انجام نشد', 'فایل موجود نیست');

            return back();

        } catch (\Exception $e) {
            Log::error('Download error: '.$e->getMessage());
            ToastMagic::error('انجام نشد', 'مشکلی در فرآیند بارگیری پیش آمد');

            return back();
        }
    }
}
