<?php

namespace App\Http\Controllers;

use App\Models\{Event, Khabar, Magazine, Scope, Category};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Log, Storage};
use Devrabiul\ToastMagic\Facades\ToastMagic;

class WriterController extends Controller
{
    /* ======================
       ==== Views ====
    ====================== */

    public function magazineCreateView()
    {
        return view('writer-panel.magazineCreate', [
            'categories' => Category::all(),
            'magazine' => null
        ]);
    }

    public function magazineUpdateView(Magazine $Magazine)
    {
        return view('writer-panel.magazineEdit', [
            'categories' => Category::all(),
            'Magazine' => $Magazine
        ]);
    }

    public function newsCreateView()
    {
        return view('writer-panel.news_create', [
            'categories' => Category::all(),
            'scopes' => Scope::withCount('news')->pluck('name', 'id')
        ]);
    }

    public function newsUpdateView(Khabar $Khabar)
    {
        return view('writer-panel.newEdit', [
            'categories' => Category::all(),
            'Khabar' => $Khabar
        ]);
    }

    public function eventCreateView()
    {
        return view('writer-panel.event_create', [
            'categories' => Category::all()
        ]);
    }

    public function eventUpdateView(Event $event)
    {
        return view('writer-panel.eventEdit', [
            'event' => $event,
            'categories' => Category::all()
        ]);
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

            "category" => "nullable|array",
            "category.*" => "exists:categories,id",

            "articles" => "nullable|array",
            "articles.*.title" => "required|min:6|max:100",
            "articles.*.author" => "required|string|min:2|max:50",
            "articles.*.abstract" => "required|min:15|max:10000",
            "articles.*.body" => "required|min:15",
            "articles.*.addOn" => "nullable|file|mimes:pdf,docx|max:10000",
        ]);

        DB::beginTransaction();
        try {
            // ذخیره فایل‌ها
            $imagePath = $request->file('image')->store('images', 'public');
            $pdfPath   = $request->file('addOn')->store('attachments', 'public');

            // ایجاد نشریه
            $magazine = Auth::user()->magazines()->create([
                "title" => $data['title'],
                "image" => $imagePath,
                "pdf"   => $pdfPath,
                "body"  => $data['desc'] ?? '',
            ]);

            // دسته‌بندی‌ها
            $magazine->categories()->attach($data['category'] ?? []);

            // مقالات
            if (!empty($data['articles'])) {
                foreach ($data['articles'] as $i => $article) {
                    $file = $request->hasFile("articles.$i.addOn")
                        ? $request->file("articles.$i.addOn")->store("attachments", "public")
                        : null;

                    $magazine->articles()->create([
                        "title" => $article["title"],
                        "author" => $article["author"],
                        "abstract" => $article["abstract"],
                        "body" => $article["body"],
                        "url" => $file
                    ]);
                }
            }

            DB::commit();
            ToastMagic::success("نشریه با موفقیت ایجاد شد");
            return redirect()->route('home');

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("Magazine create error", ['exception' => $th]);
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
            "category" => "nullable|array",
            "category.*" => "exists:categories,id",
            "scope" => "nullable|exists:scopes,id",
        ]);

        try {
            $payload = [
                "title" => $data['title'],
                "body" => $data['body'],
                "user_id" => Auth::id(),
                "image" => $request->file('image')?->store('images', 'public'),
                "pdf" => $request->file('addOn')?->store('attachments', 'public'),
            ];

            if (!empty($data['scope'])) {
                $news = Scope::find($data['scope'])->news()->create($payload);
            } else {
                $news = Khabar::create($payload);
            }

            if (!empty($data['category'])) {
                $news->categories()->attach($data['category']);
            }

            ToastMagic::success("خبر با موفقیت ایجاد شد");
            return redirect()->back();

        } catch (\Throwable $th) {
            Log::error("News create error", ['exception' => $th]);
            ToastMagic::error("خطا در ایجاد خبر");
            return redirect()->back()->withInput();
        }
    }

    public function eventCreate(Request $request)
    {
        $data = $request->validate([
            "title" => "required|min:6|max:100",
            "body" => "required|min:15|max:100000",
            "image" => "required|image|mimes:jpeg,jpg,png,svg|max:2048",
            "category" => "nullable|array",
            "category.*" => "exists:categories,id",
        ]);

        try {
            $event = Auth::user()->events()->create([
                "title" => $data['title'],
                "body" => $data['body'],
                "image" => $request->file('image')->store('images', 'public'),
            ]);

            $event->categories()->attach($data['category'] ?? []);

            ToastMagic::success("رویداد با موفقیت ایجاد شد");
            return redirect()->back();
        } catch (\Throwable $th) {
            Log::error("Event create error", ['exception' => $th]);
            ToastMagic::error("خطا در ایجاد رویداد");
            return redirect()->back()->withInput();
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
            "category" => "nullable|array",
            "category.*" => "exists:categories,id",

            "articles" => "nullable|array",
            "articles.*.title" => "required|min:6|max:100",
            "articles.*.author" => "required|string|min:2|max:50",
            "articles.*.abstract" => "required|min:15|max:10000",
            "articles.*.body" => "required|min:15",
            "articles.*.addOn" => "nullable|file|mimes:pdf,docx|max:10000",
        ]);

        $magazine = Magazine::findOrFail($data['id']);

        DB::beginTransaction();
        try {
            $payload = [
                "title" => $data['title'],
                "body" => $data['body'] ?? '',
            ];

            if ($request->hasFile('image')) {
                Storage::disk('public')->delete($magazine->image);
                $payload['image'] = $request->file('image')->store('images', 'public');
            }
            if ($request->hasFile('addOn')) {
                Storage::disk('public')->delete($magazine->pdf);
                $payload['pdf'] = $request->file('addOn')->store('attachments', 'public');
            }

            $magazine->update($payload);

            $magazine->categories()->sync($data['category'] ?? []);

            foreach ($magazine->articles as $article) {
                if ($article->url) {
                    Storage::disk('public')->delete($article->url);
                }
                $article->delete();
            }

            if (!empty($data['articles'])) {
                foreach ($data['articles'] as $i => $article) {
                    $file = $request->hasFile("articles.$i.addOn")
                        ? $request->file("articles.$i.addOn")->store("attachments", "public")
                        : null;

                    $magazine->articles()->create([
                        "title" => $article['title'],
                        "author" => $article['author'],
                        "abstract" => $article['abstract'],
                        "body" => $article['body'],
                        "url" => $file,
                    ]);
                }
            }

            DB::commit();
            ToastMagic::success("نشریه با موفقیت بروزرسانی شد");
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
        $table = (new $modelClass)->getTable();
        $data = $request->validate([
            "id" => "required|exists:$table,id",
            "title" => "required|min:6|max:100",
            "body" => "required|min:15",
            "image" => "nullable|image|max:4048",
            "addOn" => "nullable|file|mimes:pdf,docx|max:5000",
            "category" => "nullable|array",
            "category.*" => "exists:categories,id",
        ]);

        $instance = $modelClass::findOrFail($data['id']);

        try {
            $payload = [
                "title" => $data['title'],
                "body" => $data['body'],
            ];
            if ($request->hasFile('image')) {
                Storage::disk('public')->delete($instance->image);
                $payload['image'] = $request->file('image')->store('images', 'public');
            }
            if ($request->hasFile('addOn')) {
                Storage::disk('public')->delete($instance->pdf);
                $payload['pdf'] = $request->file('addOn')->store('attachments', 'public');
            }

            $instance->update($payload);

            $instance->categories()->sync($data['category'] ?? []);

            ToastMagic::success("$type با موفقیت بروزرسانی شد");
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

        if ($content && ($content->user_id == $user->id || $user->hasAnyRole(['admin', 'super admin']))) {
            Storage::disk('public')->delete($content->image);
            Storage::disk('public')->delete($content->pdf ?? '');

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

