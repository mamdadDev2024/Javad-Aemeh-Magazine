<?php

namespace App\Http\Controllers;

use App\Helpers\SweetAlert2;
use App\Models\Article;
use App\Models\Category;
use App\Models\Khabar;
use App\Models\Event;
use App\Models\Magazine;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ShowController extends Controller
{
    public function articleShow($slug)
    {

        return $this->handleArticleShow($slug, Article::class ,'Article');
    }
    public function magazineShow($slug)
    {

        return $this->handleShowMagazine($slug, Magazine::class ,'Magazine');
    }
    public function newsShow($slug)
    {
        return $this->handleShow($slug, Khabar::class , 'Khabar');
    }
    public function eventShow($slug)
    {
        return $this->handleShow($slug, Event::class , 'Event');
    }
    private function handleArticleShow($slug, $model , string $type)
    {
        try {
            $article = $model::where("slug" , $slug)
                ->withCount('comments', 'views')
                ->with([
                    'user',
                    'comments' => function ($query) {
                        $query->where('status', 1);
                    },
                ])
                ->first();
            if (!$article) {
                session()->flash('alert', SweetAlert2::alert('موجود نیست!', 'شاید آدرسو اشتباه رفتی!', 'error'));
                return redirect()->back();
            }


            if (Auth::check()) {
                $article->views()->firstOrCreate([
                    'ip_address' => request()->ip(),
                    'user_id' => Auth::id()
                ]);
            }

            $article['like_count'] = $article->likers()->count();
            $relateds = $article->categories()->first()
            ? $article->categories()->first()->articles()->where('status', 1)->with('user')->limit(10)->get()
            : $model::where('status', 1)->with('user')->limit(10)->get();

            return view('show.ArticleShow', compact('article', 'relateds', 'type'));
        } catch (\Throwable $th) {
            Log::error("Error in show logic for {$type}: " . $th->getMessage());
            session()->flash('alert', SweetAlert2::alert('موجود نیست!', 'شاید آدرسو اشتباه رفتی!', 'error'));
            return redirect()->back();
        }
    }
    private function handleShow($slug, $model , string $type)
    {
        try {
            $item = $model::where("slug" , $slug)
                ->withCount('comments', 'views')
                ->with([
                    'user',
                    'comments' => function ($query) {
                        $query->where('status', 1);
                    },
                ])
                ->first();
            if (!$item) {
                session()->flash('alert', SweetAlert2::alert('موجود نیست!', 'شاید آدرسو اشتباه رفتی!', 'error'));
                return redirect()->back();
            }


            if (Auth::check()) {
                $item->views()->firstOrCreate([
                    'ip_address' => request()->ip(),
                    'user_id' => Auth::id()
                ]);
            }

            $item['like_count'] = $item->likers()->count();
            $relateds = $item->categories()->first()
            ? $item->categories()->first()->articles()->with('user')->limit(10)->get()
            : $model::with('user')->limit(10)->get();
            $categories = $item->categories()->get()->toArray();
            return view('show.ContentShow', compact('item', 'relateds', 'type' , "categories"));
        } catch (\Throwable $th) {
            Log::error("Error in show logic for {$type}: " . $th->getMessage());
            session()->flash('alert', SweetAlert2::alert('موجود نیست!', 'شاید آدرسو اشتباه رفتی!', 'error'));
            return redirect()->back();
        }
    }
    private function handleShowMagazine($slug, $model , string $type)
    {
        try {
            $magazine = $model::where("slug" , $slug)
                ->withCount('comments', "likers as like_count" , 'views')
                ->with([
                    'user',
                    'articles',
                    'comments' => function ($query) {
                        $query->where('status', 1);
                    },
                ])
                ->first();

            if (!$magazine) {
                session()->flash('alert', SweetAlert2::alert('موجود نیست!', 'شاید آدرسو اشتباه رفتی!', 'error'));
                return redirect()->back();
            }


            if (Auth::check()) {
                $magazine->views()->firstOrCreate([
                    'ip_address' => request()->ip(),
                    'user_id' => Auth::id()
                ]);
            }

            $relateds = $magazine->categories()->first()
            ? $magazine->categories()->first()->articles()->with('user')->limit(10)->get()->except($magazine->id)
            : $model::with('user')->limit(10)->get();
            $categories = $magazine->categories()->get()->toArray();
            return view('show.MagazineShow', compact('magazine', 'relateds', 'type' , "categories"));
        } catch (\Throwable $th) {
            Log::error("Error in show logic for {$type}: " . $th->getMessage());
            session()->flash('alert', SweetAlert2::alert('موجود نیست!', 'شاید آدرسو اشتباه رفتی!', 'error'));
            return redirect()->back();
        }
    }
}
