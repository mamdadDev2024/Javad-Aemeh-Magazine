<?php
namespace App\View\Components;

use Illuminate\View\Component;

class SwiperContainer extends Component
{
    public $items;
    public $type;
    public $defaultLink;
    public $containerClass;
    public $isMobile;

    public function __construct($items, $type, $defaultLink, $containerClass)
    {
        $this->items = $items;
        $this->type = $type;
        $this->defaultLink = $defaultLink;
        $this->containerClass = $containerClass;
        $this->isMobile = $this->isMobile();
    }
    private function isMobile(): bool
    {
        $userAgent = request()->header('User-Agent');
        $mobileAgents = ['Android', 'iPhone', 'iPad', 'iPod', 'webOS', 'BlackBerry', 'Windows Phone', 'Opera Mini', 'IEMobile'];

        foreach ($mobileAgents as $agent) {
            if (stripos($userAgent, $agent) !== false) {
                return true;
            }
        }

        return false;
    }
    private function adjustSlides(array $items, int $limit)
    {
        $count = count($items);

        if ($count < $limit && !$this->isMobile) {
            $defaultSlides = array_fill(0, $limit - $count, [
                'title' => 'موسسه جواد الائمه',
                'body' => '',
                'image' => asset('assets/logo.jpeg'),
                'slug' => route(strtolower($this->type.'s')),
            ]);
            $items = array_merge($items, $defaultSlides);
        }

        return $items;
    }

    public function render()
    {
        $this->items = $this->adjustSlides($this->items, 4);
        return view('components.swiper_container');
    }
}
