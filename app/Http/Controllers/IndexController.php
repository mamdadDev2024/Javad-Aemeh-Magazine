<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Event;
use App\Models\Khabar;
use App\Models\Magazine;

class IndexController extends Controller
{
    public function news()
    {
        $news = Khabar::withCount(["views", "likers as likes_count"])
            ->paginate(20);
        return view('index.news', compact('news'));
    }
    public function events()
    {
        $events = Event::withCount(["views", "likers as likes_count"])
            ->paginate(20);
        return view('index.events', compact('events'));
    }
    public function magazines()
    {
        $magazines = Magazine::withCount(["views", "user" , "articles" , "likers as likes_count"])
            ->paginate(20);
        return view('index.magazines', compact('magazines'));
    }
}
