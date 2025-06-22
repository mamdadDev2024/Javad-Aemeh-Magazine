<?php

namespace App\Http\Controllers;

use App\Helpers\SweetAlert2;
use App\Models\Event;
use App\Models\Khabar;
use App\Models\Magazine;
use App\Models\Scope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Mews\Purifier\Facades\Purifier;

class WriterController extends Controller
{
    public function magazineCreateView()
    {
        $categories = \App\Models\Category::all()->toArray();
        return view("writer-panel.magazineCreate", compact("categories"));
    }

    public function newCreateView()
    {
        $categories = \App\Models\Category::all()->toArray();
        $scopes = Scope::withCount("news")->pluck("name", "id")->toArray();
        return view("writer-panel.news_create", compact("categories", "scopes"));
    }

    public function eventCreateView()
    {
        $categories = \App\Models\Category::all()->toArray();
        return view("writer-panel.event_create", compact("categories"));
    }

    public function eventUpdateView(Event $Event)
    {
        $categories = \App\Models\Category::all()->toArray();
        return view("writer-panel.eventEdit", compact("Event", "categories"));
    }

    public function newUpdateView(Khabar $Khabar)
    {
        $categories = \App\Models\Category::all()->toArray();
        return view("writer-panel.newEdit", compact("Khabar", "categories"));
    }

    public function magazineUpdateView(Magazine $Magazine)
    {
        $categories = \App\Models\Category::all()->toArray();
        return view("writer-panel.magazineEdit", compact("Magazine", "categories"));
    }

    public function magazineCreate(Request $request)
    {
            $data = $request->validate([
                'g-recaptcha-response' => 'required|captcha',
                "title" => "required|min:6|max:100",
                "image" => "required|image|mimes:jpg,jpeg|max:4000",
                "addOn" => "required|file|mimes:pdf,docx|max:5000",
                "category" => "nullable|array|exists:categories,id",
                "desc" => "nullable|string|min:10|max:4000",
                "articles" => "nullable|array",
                "articles.*.addOn" => "nullable|file|mimes:pdf,docx|max:5000",
                "articles.*.title" => "required|min:6|max:100",
                "articles.*.author" => "required|string|min:2|max:50",
                "articles.*.abstract" => "required|min:15|max:10000",
                "articles.*.text" => "required|min:15",
            ], [
                'g-recaptcha-response.required' => 'لطفاً تأیید کنید که شما ربات نیستید.',
                'g-recaptcha-response.captcha' => 'اعتبارسنجی reCAPTCHA ناموفق بود. لطفاً دوباره امتحان کنید.',
            ]);

        DB::beginTransaction();
        try {
            $imagePath = $request->file('image')->store("images", "public");
            $addOnPath = $request->file('addOn')->store("attachments", "public");
            $cleanedContent = Purifier::clean($data["desc"] ?? "");

            $magazine = Auth::user()->magazines()->create([
                "title" => $data["title"],
                "slug" => Str::slug($data["title"], '-') ?: uniqid(),
                "image" => $imagePath,
                "pdf" => $addOnPath,
                "body" => $cleanedContent
            ]);

            if (!empty($data["category"])) {
                $categories = array_unique(array_filter($data["category"], 'is_numeric'));
                $magazine->categories()->attach($categories);
            }

            if (!empty($data['articles'])) {
                foreach ($data['articles'] as $article) {
                    $addOnPathArticle = $request->hasFile($article['addOn']) ? $article["addOn"]->store("attachments", "public") : null;
                    $magazine->articles()->create([
                        "title" => $article['title'],
                        "author" => $article["author"],
                        "url" => $addOnPathArticle,
                        "abstract" => $article['abstract'],
                        "text" => $article['text'],
                        "slug" => Str::slug($article["title"], '-') ?: uniqid()
                    ]);
                }
            }

            DB::commit();
            session()->flash("alert", SweetAlert2::alert("انجام شد", "نشریه با موفقیت ایجاد شد"));
            return redirect()->route('home');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("Error creating magazine: {$th->getMessage()}");
            session()->flash("alert", SweetAlert2::alert("خطا", "مشکلی پیش آمد. لطفا بعدا تلاش کنید.", "error"));
            return redirect()->back()->withInput();
        }
    }


    public function newCreate(Request $request)
    {
        $data = $request->validate([
            'g-recaptcha-response' => 'required|captcha',
            "title" => "required|min:6|max:100",
            "body" => "required|min:15|max:100000",
            "image" => "file|image|mimes:jpeg,jpg,svg|max:2048",
            "addOn" => "nullable|file|mimes:pdf,docx|max:10000",
            "category" => "array|nullable|exists:categories,id",
            "scope" => "nullable|exists:scopes,id"
        ], [
            'g-recaptcha-response.required' => 'لطفاً تأیید کنید که شما ربات نیستید.',
            'g-recaptcha-response.captcha' => 'اعتبارسنجی reCAPTCHA ناموفق بود. لطفاً دوباره امتحان کنید.',]
            );

        try {
            $scope = isset($data["scope"]) ? Scope::findOrFail($data["scope"]) : null;
            $image = $request->file("image");
            $addOnPath = $request->hasFile('addOn') ? $request->file("addOn")->store("attachments", "public") : null;
            $imagePath = $image->store("images", "public");

            $news = $scope ? $scope->news()->create([
                "title" => $data["title"],
                "body" => $data["body"],
                "slug" => Str::slug($data["title"]),
                "image" => $imagePath,
                "pdf" => $addOnPath,
                "user_id" => Auth::id()
            ]) : Khabar::create([
                "title" => $data["title"],
                "body" => $data["body"],
                "slug" => Str::slug($data["title"]),
                "image" => $imagePath,
                "pdf" => $addOnPath,
                "user_id" => Auth::id()
            ]);

            session()->flash("alert", SweetAlert2::alert("انجام شد", "خبر مورد نظر با موفقیت ثبت شد"));
            return redirect()->back();
        } catch (\Throwable $th) {
            Log::error("Error while creating news: " . $th);
            session()->flash("alert", SweetAlert2::alert("انجام نشد", "متاسفانه در پردازشات مشکلی به وجود آمد لطفا بعدا امتحان کنید", "error"));
            return redirect()->back();
        }
    }

    public function eventCreate(Request $request)
    {
        $data = $request->validate([
            'g-recaptcha-response' => 'required|captcha',
            "title" => "required|min:6|max:100",
            "body" => "required|min:15|max:100000",
            "image" => "required|image|mimes:jpeg,jpg,svg|max:2048",
            "category" => "array|nullable|exists:categories,id",
        ], [
            'g-recaptcha-response.required' => 'لطفاً تأیید کنید که شما ربات نیستید.',
            'g-recaptcha-response.captcha' => 'اعتبارسنجی reCAPTCHA ناموفق بود. لطفاً دوباره امتحان کنید.',
            ]);
        try {
            $user = Auth::user();
            $image = $request->file("image");
            $imagePath = $image->store("images", "public");
            $event = $user->events()->create([
                "title" => $data["title"],
                "body" => $data["body"],
                "slug" => Str::slug($data["title"]),
                "image" => $imagePath,
            ]);
            session()->flash("alert", SweetAlert2::alert("انجام شد", "رویداد مورد نظر با موفقیت ثبت شد"));
            return redirect()->back();
        } catch (\Throwable $th) {
            Log::error("Error while creating event: " . $th);
            session()->flash("alert", SweetAlert2::alert("انجام نشد", "متاسفانه در پردازشات مشکلی به وجود آمد لطفا بعدا امتحان کنید", "error"));
            return redirect()->back();
        }
    }
    public function magazineUpdate(Request $request)
    {
        return $this->MagazineUpdateHandler($request);
    }

    public function newUpdate(Request $request)
    {
        return $this->UpdateHandler($request, Khabar::class, "khabar");
    }

    public function eventUpdate(Request $request)
    {
        return $this->UpdateHandler($request, Event::class, "event");
    }
    public function articleDestroy(string $id)
    {
        return $this->DestroyModel($id, Magazine::class);
    }

    public function eventDestroy(string $id)
    {
        return $this->DestroyModel($id, Event::class);
    }

    public function newsDestroy($id)
    {
        return $this->DestroyModel($id, Khabar::class);
    }
    private function MagazineUpdateHandler(Request $request)
    {

        $data = $request->validate([
            // "g-recaptcha-response"=>"required|captcha",
            "id" => "required|exists:magazines,id",
            "title" => "required|string|min:6|max:100",
            "image" => "nullable|image|max:4000",
            "desc" => "nullable|string|min:10",
            "addOn" => "nullable|file|mimes:pdf,docx|max:5000",
            "category" => "nullable|array|exists:categories,id",
            "articles" => "nullable|array",
            "articles.*.addOn" => "nullable|file|mimes:pdf,docx|max:5000",
            "articles.*.existing_file" => "nullable|exists:articles,url",
            "articles.*.author" => "required|string|min:2|max:50",
            "articles.*.title" => "required|string|min:6|max:100",
            "articles.*.abstract" => "required|string|min:15|max:10000",
            "articles.*.text" => "required|min:15|string|max:100000",
        ]);
        $magazine = Magazine::findOrFail($data["id"]);
        DB::beginTransaction();
        try {
            $this->handleFiles($request, $magazine);
            $magazine->update([
                'title' => $data['title'],
                "body" => $data["desc"],
                'slug' => Str::slug($data['title']) ?: uniqid(),
            ]);
            $this->syncCategories($data['category'] ?? [], $magazine);
            $this->syncArticles($request , $data['articles'] ?? [], $magazine);
            DB::commit();
            session()->flash("alert", SweetAlert2::alert("انجام شد", "نشریه با موفقیت به‌روزرسانی شد"));
            return redirect()->route('home');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("Error updating magazine: {$th->getMessage()}");
            session()->flash("alert", SweetAlert2::alert("خطا", "مشکلی پیش آمد. لطفا بعدا تلاش کنید.", "error"));
            return redirect()->back()->withInput();
        }
    }

    private function handleFiles($request, $magazine)
    {
        if ($request->hasFile('image')) {
            $magazine->image = $request->file("image")->store("images", "public");
        }

        if ($request->hasFile('addOn')) {
            $magazine->pdf = $request->file("addOn")->store("attachments", "public");
        }
        $magazine->save();
    }

    private function syncCategories($categories, $magazine)
    {
        $categories = array_unique(array_filter($categories, 'is_numeric'));
        $magazine->categories()->sync($categories);
    }

    private function syncArticles($request, $articles, $magazine)
    {
        $magazine->articles()->delete();

        foreach ($articles as $key => $articleData) {
            $addOnPathArticle = null;
            if ($request->hasFile("articles.{$key}.addOn")) {
                $addOnPathArticle = $request->file("articles.{$key}.addOn")->store("attachments", "public");
            } elseif (!empty($articleData['existing_file'])) {
                $addOnPathArticle = $articleData['existing_file'];
            }

            $slug = Str::slug($articleData['title'], '-');
            $slug = $slug ?: uniqid();

            $magazine->articles()->create([
                'slug'    => $slug,
                'title'   => $articleData['title'],
                'author'  => $articleData['author'],
                'abstract'=> $articleData['abstract'],
                'text'    => $articleData['text'],
                'url'     => $addOnPathArticle,
            ]);
        }
    }




    private function UpdateHandler(Request $request, $model, $type)
    {
        $data = $request->validate([
            'g-recaptcha-response' => 'required|captcha',
            "title" => "required|min:6|max:100|unique:{$type}s,title," . $request->id,
            "body" => "required|min:15|max:100000",
            "image" => "nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048",
            "addOn" => "nullable|file|mimes:pdf,docx|max:10000",
            "category" => "array|nullable|exists:categories,id",
        ]);

        try {
            $instance = $model::findOrFail($request->id);
            $instance->title = $data["title"];
            $instance->body = $data["body"];

            if ($request->hasFile('image')) {
                $imagePath = $request->file("image")->store("images", "public");
                $instance->image = $imagePath;
            }

            if ($request->hasFile('addOn')) {
                $addOnPath = $request->file("addOn")->store("attachments", "public");
                $instance->pdf = $addOnPath;
            }

            if (!empty($data["category"])) {
                $categories = array_unique(array_filter($data["category"], 'is_numeric'));
                $instance->categories()->sync($categories);
            }

            $instance->save();
            session()->flash("alert", SweetAlert2::alert("انجام شد", __("types.$type")." مورد نظر با موفقیت به‌روزرسانی شد"));
            return redirect()->route('home');
        } catch (\Throwable $th) {
            Log::error("Error updating {$type}: {$th->getMessage()}");
            session()->flash("alert", SweetAlert2::alert("خطا", "مشکلی پیش آمد. لطفا بعدا تلاش کنید.", "error"));
            return redirect()->back()->withInput();
        }
    }

    private function DestroyModel($id, $model)
    {
        $content = $model::find($id);
        $user = Auth::user();
        if ($content && ($content->user_id == $user->id || $user->hasRole("admin|super admin"))) {
            $content->delete();
            session()->flash("alert", SweetAlert2::alert("حذف شد", "محتوای مورد نظر حذف شد"));
            return redirect()->back();
        } else {
            session()->flash("alert", SweetAlert2::alert("عملیات شکست خورد", "حذف انجام نشد", "error"));
            return redirect()->back();
        }
    }
}
