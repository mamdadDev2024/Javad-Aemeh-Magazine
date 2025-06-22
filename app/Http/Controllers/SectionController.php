<?php

namespace App\Http\Controllers;

use App\Helpers\SweetAlert2;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SectionController extends Controller
{
    public function editSection($name, Request $request)
    {
        $section = Section::where('name', $name)->firstOrFail();
        $fieldName = $section->name;

        try {
            if (!in_array($fieldName, ["titleHeader", "titleFooter"])) {
                $data = $request->validate([
                    "captcha"=>"required|captcha",
                    $fieldName => "required|string|max:1000",
                ]);
                $section->content = $data[$fieldName];
            } else {
                $data = $request->validate([
                    "captcha"=>"required|captcha",
                    $fieldName => "required|image|max:1024",
                ]);

                if ($section->content && Storage::exists($section->content)) {
                    Storage::delete($section->content);
                }

                $imagePath = $request->file($fieldName)->store("images" , "public");
                $section->content = $imagePath;
            }

            $section->save();
            Log::info('Section updated successfully', ['section_name' => $name]);
            session()->flash("alert", SweetAlert2::alert("انجام شد", "قسمت مورد نظر تغییر کرد!"));
            return back();
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation failed for section update', [
                'section_name' => $name,
                'errors' => $e->errors(),
            ]);
            session()->flash("alert", SweetAlert2::alert("خطا", "اعتبارسنجی ناموفق بود", "error"));
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Section update failed: ' . $e->getMessage(), ['section_name' => $name]);
            session()->flash("alert", SweetAlert2::alert("خطا", "مشکلی پیش آمد", "error"));
            return back();
        }
    }


}
