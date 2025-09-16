<?php

namespace App\Http\Controllers;

use App\Models\{Event, Khabar, Magazine, Scope, Category};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Log};
use Illuminate\Support\Str;
use Devrabiul\ToastMagic\Facades\ToastMagic;

class WriterController extends Controller
{
    /* ======================
       ==== Views ====
    ====================== */
    public function magazineCreateView()
    {
        $categories = Category::all();
        $magazine = null;
        return view('writer-panel.magazineCreate', compact('categories' , 'magazine'));
    }

    public function magazineUpdateView(Magazine $Magazine)
    {
        $categories = Category::all();
        return view('writer-panel.magazineEdit', compact('Magazine', 'categories'));
    }

    public function newsCreateView()
    {
        $categories = Category::all();
        $scopes = Scope::withCount('news')->pluck('name', 'id');
        return view('writer-panel.news_create', compact('categories', 'scopes'));
    }

    public function newsUpdateView(Khabar $khabar)
    {
        $categories = Category::all();
        return view('writer-panel.newEdit', compact('khabar', 'categories'));
    }

    public function eventCreateView()
    {
        $categories = Category::all();
        return view('writer-panel.event_create', compact('categories'));
    }

    public function eventUpdateView(Event $event)
    {
        $categories = Category::all();
        return view('writer-panel.eventEdit', compact('event', 'categories'));
    }

    /* ======================
       ==== Create ====
    ====================== */
    public function magazineCreate(Request $request)
    {
        $data = $request->validate([
            "title" => "required|min:6|max:100",
            "desc" => "nullable|string|min:10|max:4000",
            "image" => "required|image|mimes:jpeg,jpg,png|max:4048",
            "addOn" => "required|file|mimes:pdf,docx|max:10000",
            "category" => "nullable|array|exists:categories,id",
            "articles" => "nullable|array",
            "articles.*.addOn" => "nullable|file|mimes:pdf,docx|max:10000",
            "articles.*.title" => "required|min:6|max:100",
            "articles.*.author" => "required|string|min:2|max:50",
            "articles.*.abstract" => "required|min:15|max:10000",
            "articles.*.body" => "required|min:15",
        ]);

        DB::beginTransaction();
        try {
            $magazine = Auth::user()->magazines()->create([
                "title" => $data['title'],
                "slug" => Str::slug($data['title']) ?: uniqid(),
                "image" => $request->file('image')->store('images', 'public'),
                "pdf" => $request->file('addOn')->store('attachments', 'public'),
                "body" => $data['desc'] ?? '',
            ]);

            if (!empty($data['category'])) {
                $magazine->categories()->attach($data['category']);
            }

            if (!empty($data['articles'])) {
                foreach ($data['articles'] as $idx => $article) {
                    $magazine->articles()->create([
                        "title" => $article['title'],
                        "author" => $article['author'],
                        "abstract" => $article['abstract'],
                        "body" => $article['body'],
                        "url" => $request->hasFile("articles.$idx.addOn")
                            ? $request->file("articles.$idx.addOn")->store("attachments", "public")
                            : null,
                        "slug" => Str::slug($article['title']) ?: uniqid(),
                    ]);
                }
            }

            DB::commit();
            ToastMagic::success("نشریه با موفقیت ایجاد شد");
            return redirect()->route('home');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("Magazine create error: {$th->getMessage()}");
            ToastMagic::error("خطا در ایجاد نشریه");
            return redirect()->back()->withInput();
        }
    }

    public function newsCreate(Request $request)
    {
        $data = $request->validate([
            "title" => "required|min:6|max:100",
            "body" => "required|min:15|max:100000",
            "image" => "nullable|image|mimes:jpeg,jpg,png,svg|max:2048",
            "addOn" => "nullable|file|mimes:pdf,docx|max:10000",
            "category" => "nullable|array|exists:categories,id",
            "scope" => "nullable|exists:scopes,id",
        ]);

        try {
            $scope = isset($data['scope']) ? Scope::find($data['scope']) : null;
            $newsModel = $scope ? $scope->news() : new Khabar();

            $newsModel->create([
                "title" => $data['title'],
                "body" => $data['body'],
                "slug" => Str::slug($data['title']),
                "image" => $request->file('image')?->store('images', 'public'),
                "pdf" => $request->file('addOn')?->store('attachments', 'public'),
                "user_id" => Auth::id(),
            ]);

            ToastMagic::success("خبر با موفقیت ایجاد شد");
            return redirect()->back();
        } catch (\Throwable $th) {
            Log::error("News create error: {$th->getMessage()}");
            ToastMagic::error("خطا در ایجاد خبر");
            return redirect()->back();
        }
    }

    public function eventCreate(Request $request)
    {
        $data = $request->validate([
            "title" => "required|min:6|max:100",
            "body" => "required|min:15|max:100000",
            "image" => "required|image|mimes:jpeg,jpg,png,svg|max:2048",
            "category" => "nullable|array|exists:categories,id",
        ]);

        try {
            $event = Auth::user()->events()->create([
                "title" => $data['title'],
                "body" => $data['body'],
                "slug" => Str::slug($data['title']),
                "image" => $request->file('image')->store('images', 'public'),
            ]);

            if (!empty($data['category'])) {
                $event->categories()->attach($data['category']);
            }

            ToastMagic::success("رویداد با موفقیت ایجاد شد");
            return redirect()->back();
        } catch (\Throwable $th) {
            Log::error("Event create error: {$th->getMessage()}");
            ToastMagic::error("خطا در ایجاد رویداد");
            return redirect()->back();
        }
    }

    /* ======================
       ==== Update ====
    ====================== */
    public function magazineUpdate(Request $request)
    {
        $data = $request->validate([
            "id" => "required|exists:magazines,id",
            "title" => "required|min:6|max:100",
            "body" => "nullable|string|min:10",
            "image" => "nullable|image|max:4048",
            "addOn" => "nullable|file|mimes:pdf,docx|max:10000",
            "category" => "nullable|array|exists:categories,id",
            "articles" => "nullable|array",
            "articles.*.addOn" => "nullable|file|mimes:pdf,docx|max:10000",
            "articles.*.title" => "required|min:6|max:100",
            "articles.*.author" => "required|string|min:2|max:50",
            "articles.*.abstract" => "required|min:15|max:10000",
            "articles.*.body" => "required|min:15",
        ]);

        $magazine = Magazine::findOrFail($data['id']);

        DB::beginTransaction();
        try {
            // فایل‌ها
            if ($request->hasFile('image')) {
                $magazine->image = $request->file('image')->store('images', 'public');
            }
            if ($request->hasFile('addOn')) {
                $magazine->pdf = $request->file('addOn')->store('attachments', 'public');
            }

            // اطلاعات
            $magazine->update([
                "title" => $data['title'],
                "body" => $data['body'] ?? '',
                "slug" => Str::slug($data['title']) ?: uniqid(),
            ]);

            // دسته‌بندی‌ها
            $magazine->categories()->sync($data['category'] ?? []);

            // مقالات
            $magazine->articles()->delete();
            if (!empty($data['articles'])) {
                foreach ($data['articles'] as $idx => $article) {
                    $magazine->articles()->create([
                        "title" => $article['title'],
                        "author" => $article['author'],
                        "abstract" => $article['abstract'],
                        "body" => $article['body'],
                        "url" => $request->hasFile("articles.$idx.addOn")
                            ? $request->file("articles.$idx.addOn")->store("attachments", "public")
                            : $article['existing_file'] ?? null,
                        "slug" => Str::slug($article['title']) ?: uniqid(),
                    ]);
                }
            }

            DB::commit();
            ToastMagic::success("نشریه با موفقیت به‌روزرسانی شد");
            return redirect()->route('home');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("Magazine update error: {$th->getMessage()}");
            ToastMagic::error("خطا در بروزرسانی نشریه");
            return redirect()->back()->withInput();
        }
    }

    public function newsUpdate(Request $request)
    {
        return $this->updateGeneric($request, Khabar::class, "خبر");
    }

    public function eventUpdate(Request $request)
    {
        return $this->updateGeneric($request, Event::class, "رویداد");
    }

    private function updateGeneric(Request $request, $modelClass, $type)
    {
        $data = $request->validate([
            "id" => "required|exists:".$modelClass::class.",id",
            "title" => "required|min:6|max:100",
            "body" => "required|min:15",
            "image" => "nullable|image|max:4048",
            "addOn" => "nullable|file|mimes:pdf,docx|max:5000",
            "category" => "nullable|array|exists:categories,id",
        ]);

        $instance = $modelClass::findOrFail($data['id']);

        try {
            if ($request->hasFile('image')) {
                $instance->image = $request->file('image')->store('images', 'public');
            }
            if ($request->hasFile('addOn')) {
                $instance->pdf = $request->file('addOn')->store('attachments', 'public');
            }

            $instance->update([
                "title" => $data['title'],
                "body" => $data['body'],
                "slug" => Str::slug($data['title']) ?: uniqid(),
            ]);

            if (!empty($data['category'])) {
                $instance->categories()->sync($data['category']);
            }

            ToastMagic::success("$type با موفقیت به‌روزرسانی شد");
            return redirect()->route('home');
        } catch (\Throwable $th) {
            Log::error("$type update error: {$th->getMessage()}");
            ToastMagic::error("خطا در بروزرسانی $type");
            return redirect()->back()->withInput();
        }
    }

    /* ======================
       ==== Delete ====
    ====================== */
    public function destroy($id, $modelClass)
    {
        $content = $modelClass::find($id);
        $user = Auth::user();

        if ($content && ($content->user_id == $user->id || $user->hasRole('admin|super admin'))) {
            $content->delete();
            ToastMagic::success("حذف شد", "محتوا با موفقیت حذف شد");
        } else {
            ToastMagic::error("عملیات شکست خورد", "امکان حذف محتوا وجود ندارد");
        }

        return redirect()->back();
    }

    public function magazineDestroy($id)
    {
        return $this->destroy($id, Magazine::class);
    }

    public function newsDestroy($id)
    {
        return $this->destroy($id, Khabar::class);
    }

    public function eventDestroy($id)
    {
        return $this->destroy($id, Event::class);
    }
}
