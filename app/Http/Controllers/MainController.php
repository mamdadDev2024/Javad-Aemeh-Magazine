<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\Contact;
use App\Models\Event;
use App\Models\Khabar;
use App\Models\Magazine;
use App\Models\Section;
use Devrabiul\ToastMagic\Facades\ToastMagic;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Facades\Agent;

class MainController extends Controller
{
    public function landing()
    {
        $isMobile = Agent::isMobile();

        // Cache data retrieval
        $magazines = Magazine::latest()->limit(5)->get();

        $events = Event::select(['id', 'title', 'image', 'slug'])
            ->latest()
            ->limit(5)
            ->get();

        $khabars = Khabar::select(['id', 'title', 'image', 'slug'])
            ->latest()
            ->limit(5)
            ->get();

        // Get default image from sections
        $sections = Section::all()->keyBy('name');
        $defaultImage = $sections->get('defaultContentImage')->content ?? 'default-image-path.jpg';

        // Adjust slides using the default image
        $magazines = $this->adjustSlides($magazines, 3, $isMobile, $defaultImage);
        $events = $this->adjustSlides($events, 4, $isMobile, $defaultImage);
        $khabars = $this->adjustSlides($khabars, 4, $isMobile, $defaultImage);

        return view('main.landing', compact('sections', 'events', 'magazines', 'khabars', 'isMobile'));
    }

    private function adjustSlides($items, int $limit, bool $isMobile, string $defaultImage)
    {
        $count = $items->count();

        if (! $isMobile && $count < $limit) {
            $defaultSlide = [
                'title' => 'موسسه جواد الائمه',
                'image' => $defaultImage,
                'slug' => null,
                'is_default' => true,
            ];

            $items = $items->concat(array_fill(0, $limit - $count, $defaultSlide));
        }

        if ($isMobile && $count > $limit) {
            $items = $items->take($limit);
        }

        return $items;
    }

    public function contact()
    {
        return view('main.contact');
    }

    public function doContact(ContactRequest $request)
    {
        $data = $request->validated();

        try {
            if (auth()->check()) {
                Auth::user()->contacts()->create($data);
            } else {
                Contact::create($data);
            }
            ToastMagic::success('ثبت شد', 'فرم با موفقیت ارسال شد');

            return redirect()->route('home');

        } catch (\Throwable $e) {
            report($e);
            ToastMagic::error('خطا', 'در ارسال فرم خطایی رخ داد');

            return back()->withInput();
        }
    }
}
