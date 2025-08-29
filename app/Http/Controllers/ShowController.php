<?php

namespace App\Http\Controllers;

use App\Models\{Article, Category, Khabar, Event, Magazine};
use Devrabiul\ToastMagic\Facades\ToastMagic;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ShowController extends Controller
{
    public function articleShow($slug)
    {
        return $this->handleShow($slug, Article::class, 'Article', 'show.ArticleShow');
    }

    public function magazineShow($slug)
    {
        return $this->handleMagazineShow($slug, Magazine::class, 'Magazine', 'show.MagazineShow');
    }

    public function newsShow($slug)
    {
        return $this->handleShow($slug, Khabar::class, 'Khabar', 'show.ContentShow');
    }

    public function eventShow($slug)
    {
        return $this->handleShow($slug, Event::class, 'Event', 'show.ContentShow');
    }

    protected static function loadItem(string $model, string $slug, array $relations = [])
    {
        $item = $model::where("slug", $slug)
            ->withCount(['comments', 'views'])
            ->with($relations)
            ->first();

        if (!$item) {
            return null;
        }

        if (Auth::check()) {
            $item->views()->firstOrCreate([
                'ip_address' => request()->ip(),
                'user_id'    => Auth::id()
            ]);
        }

        $item['like_count'] = $item->likers()->count();

        return $item;
    }
    private function handleShow($slug, $model, string $type, string $view)
    {
        try {
            $item = self::loadItem(
                $model,
                $slug,
                [
                    'user',
                    'comments' => fn($q) => $q->where('status', 1),
                ]
            );

            if (!$item) {
                ToastMagic::error('موجود نیست!', 'شاید آدرسو اشتباه رفتی!');
                return redirect()->route('home');
            }
            $categoryIds = $item->categories()->pluck('id');
            $relateds = $categoryIds->isNotEmpty()
                ? $model::whereHas('categories', fn($q) => $q->whereIn('id', $categoryIds))
                    ->where('id', '!=', $item->id)
                    ->with('user')->limit(10)->get()
                : $model::with('user')->limit(10)->get();

            $categories = $item->categories()->get();

            return view($view, compact('item', 'relateds', 'type', 'categories'));
        } catch (\Throwable $th) {
            Log::error("Error in show logic for {$type}", [
                'slug'    => $slug,
                'user_id' => Auth::id(),
                'error'   => $th->getMessage(),
                'line'    => $th->getLine(),
                'file'    => $th->getFile(),
            ]);
            ToastMagic::error('موجود نیست!', 'شاید آدرسو اشتباه رفتی!');
            return redirect()->back();
        }
    }

    private function handleMagazineShow($slug, $model, string $type, string $view)
    {
        try {
            $magazine = $model::where("slug", $slug)
                ->withCount(['comments', 'views'])
                ->withCount(['likers as like_count'])
                ->with([
                    'user',
                    'articles',
                    'comments' => fn($q) => $q->where('status', 1),
                ])
                ->first();

            if (!$magazine) {
                ToastMagic::error('موجود نیست!', 'شاید آدرسو اشتباه رفتی!');
                return redirect()->back();
            }

            if (Auth::check()) {
                $magazine->views()->firstOrCreate([
                    'ip_address' => request()->ip(),
                    'user_id'    => Auth::id(),
                ]);
            }

            $categoryIds = $magazine->categories()->pluck('id');
            $relateds = $categoryIds->isNotEmpty()
                ? $model::whereHas('categories', fn($q) => $q->whereIn('id', $categoryIds))
                    ->where('id', '!=', $magazine->id)
                    ->with('user')->limit(10)->get()
                : $model::with('user')->limit(10)->get();

            $categories = $magazine->categories()->get();

            return view($view, compact('magazine', 'relateds', 'type', 'categories'));
        } catch (\Throwable $th) {
            Log::error("Error in show logic for {$type}", [
                'slug'    => $slug,
                'user_id' => Auth::id(),
                'error'   => $th->getMessage(),
                'line'    => $th->getLine(),
                'file'    => $th->getFile(),
            ]);
            ToastMagic::error('موجود نیست!', 'شاید آدرسو اشتباه رفتی!');
            return redirect()->back();
        }
    }

    // --- Preview Methods (for admin only) ---
    public function articlePreview($id)
    {
        $article = Article::findOrFail($id);
        $type = 'Article';
        $relateds = collect();
        return view('show.ArticleShow', compact('article', 'relateds', 'type'));
    }

    public function eventPreview($id)
    {
        $item = Event::findOrFail($id);
        $type = 'Event';
        $relateds = collect();
        $categories = $item->categories()->get();
        return view('show.ContentShow', compact('item', 'relateds', 'type', 'categories'));
    }

    public function newsPreview($id)
    {
        $item = Khabar::findOrFail($id);
        $type = 'Khabar';
        $relateds = collect();
        $categories = $item->categories()->get();
        return view('show.ContentShow', compact('item', 'relateds', 'type', 'categories'));
    }
}
