<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Http\Requests\SearchRequest;
use App\Models\{Article, Contact, Event, File, Khabar, Magazine, Report, Section};
use Devrabiul\ToastMagic\Facades\ToastMagic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Log, Storage};
use Jenssegers\Agent\Facades\Agent;
use Illuminate\Pagination\LengthAwarePaginator;

class MainController extends Controller
{
    public function landing()
    {
        $isMobile = Agent::isMobile();

        $magazines = $this->adjustSlides(Magazine::limit(5)->get()->toArray(), 3, $isMobile);
        $events = $this->adjustSlides(Event::limit(5)->get()->toArray(), 4, $isMobile);
        $khabars = $this->adjustSlides(Khabar::limit(5)->get()->toArray(), 4, $isMobile);

        $sections = Section::all()->keyBy("name")->toArray();

        return view('main.landing', compact('sections', 'events', 'magazines', 'khabars', 'isMobile'));
    }

    private function adjustSlides(array $items, int $limit, bool $isMobile): array
    {
        $count = count($items);

        if (!$isMobile && $count < $limit) {
            $defaultImage = asset(optional(Section::where("name", "defaultContentImage")->first())->content ?? 'default.jpg');
            $defaultSlide = [
                'title' => 'موسسه جواد الائمه',
                'body' => '',
                'image' => $defaultImage,
                'slug' => null,
            ];
            $items = array_merge($items, array_fill(0, $limit - $count, $defaultSlide));
        }

        if ($isMobile && $count > $limit) {
            $items = array_slice($items, 0, $limit);
        }

        return $items;
    }




    public function contact()
    {
        return view("main.contact");
    }

    public function doContact(ContactRequest $request)
    {
        $data = $request->validated();

        Auth::user()->contacts()->create([
            "body" => $data["body"]
        ]);

        ToastMagic::success("ثبت شد", "فرم ارسال شد");
        return redirect()->route("home");
    }

}
