<?php

namespace App\Http\Controllers;

use App\Helpers\SweetAlert2;
use App\Models\Article;
use App\Models\Contact;
use App\Models\Event;
use App\Models\File;
use App\Models\Khabar;
use App\Models\Magazine;
use App\Models\Report;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Jenssegers\Agent\Facades\Agent;

class MainController extends Controller
{
    public function landing()
    {
        $isMobile = Agent::isMobile();
        $magazines = Magazine::limit(5)->get()->toArray();
        $events = Event::limit(5)->get()->toArray();
        $khabars = Khabar::limit(5)->get()->toArray();

        $magazines = $this->adjustSlides($magazines, 3, $isMobile, 'magazines');
        $events = $this->adjustSlides($events, 4, $isMobile, 'events');
        $khabars = $this->adjustSlides($khabars, 4, $isMobile, 'news');

        $sections = Section::all()->keyBy("name")->toArray();

        return view('main.landing', compact('sections', 'events', 'magazines', 'khabars', 'isMobile'));
    }

    private function adjustSlides(array $items, int $limit, bool $isMobile, string $type)
    {
        $count = count($items);

        if ( !$isMobile && $count < $limit) {
            $defaultSlides = array_fill(0, $limit - $count, [
                'title' => 'موسسه جواد الائمه',
                'body' => '',
                'image' => asset(Section::where("name" , "defaultContentImage")->first()->content),
                'slug' => null,
            ]);
            $items = array_merge($items, $defaultSlides);
        }

        if ($isMobile && $count > $limit) {
            $items = array_slice($items, 0, $limit);
        }

        return $items;
    }

    public function download()
    {
        try {
            if(!empty($_GET["url"]) && !Storage::exists($_GET["url"])){
                return Storage::disk('public')->download($_GET["url"]);
            }else{
                session()->flash( "alert" ,SweetAlert2::alert("انجام نشد", "فایل موجود نیست" , "error"));
                return back();
            }
        } catch (\Exception $e) {
            Log::error('Download error: ' . $e->getMessage());

            session()->flash( "alert" ,SweetAlert2::alert("انجام نشد", "مشکلی در فرآیند بارگیری پیش آمد" , "error"));
            return back();
        }
    }

    public function search(Request $request)
    {
        $data = $request->validate([
            "search" => "nullable|string",
            "type" => "nullable|string|in:Magazine,Article,Event,New,all"
        ]);

        $search = $data["search"] ?? null;
        $type = $data["type"] ?? 'all';
        try {
            if ($type === 'all') {
                $magazines = Magazine::query();
                $events = Event::query();
                $news = Khabar::query();
                $articles = Article::query();
            } else {
                $model = match ($type) {
                    'Magazine' => Magazine::query(),
                    'Event' => Event::query(),
                    'New' => Khabar::query(),
                    "Article" => Article::query(),
                    default => Magazine::query(),
                };
            }
            if ($search) {
                if ($type === 'all') {
                    $magazines->where('title', 'like', "%" . $search . "%");
                    $events->where('title', 'like', "%" . $search . "%");
                    $news->where('title', 'like', "%" . $search . "%");
                    $articles->where('title', 'like', "%" . $search . "%");
                } else {
                    $model->where('title', 'like', "%" . $search . "%");
                }
            }
            if ($type === 'all') {
                $magazinesResults = $magazines->get();
                $eventsResults = $events->get();
                $newsResults = $news->get();
                $articlesResults = $articles->get();
                $results = $magazinesResults->merge($eventsResults)->merge($newsResults)->merge($articlesResults);
                $results = new \Illuminate\Pagination\LengthAwarePaginator(
                    $results->forPage(1, 10),
                    $results->count(),
                    10
                );
            } else {
                $results = $model->paginate(10);
            }

        } catch (\Exception $e) {
            $results = Magazine::paginate(10);
            Log::error("Search error: " . $e->getMessage());
        }
        return view("main.search", compact("results"));
    }


    public function contact()
    {
        $contacts = Contact::where("status", 0)->get();
        return view("main.contact", compact("contacts"));
    }

    public function doContact(Request $request)
    {
        $data = $request->validate([
        'g-recaptcha-response' => 'required|captcha',
            "body" => "required|min:10|max:10000|string",
            "number" => "required|min:9|numeric"
        ], [
            'g-recaptcha-response.required' => 'لطفاً تأیید کنید که شما ربات نیستید.',
            'g-recaptcha-response.captcha' => 'اعتبارسنجی reCAPTCHA ناموفق بود. لطفاً دوباره امتحان کنید.',
        ]);

        $user = Auth::user();
        $user->contacts()->create([
            "body" => $data["body"],
            "number" => $data["number"]
        ]);

        session()->flash("alert", SweetAlert2::alert("ثبت شد", "فرم ارسال شد"));
        return redirect()->route("home");
    }
}
