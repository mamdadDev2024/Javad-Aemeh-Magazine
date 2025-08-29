<?php
namespace App\View\Components;

use Illuminate\View\Component;

class SwiperContainer extends Component
{
    public $items;
    public $type;
    public $defaultLink;
    public $containerClass;

    /**
     * SwiperContainer constructor.
     *
     * @param  \Illuminate\Support\Collection|array  $items
     * @param  string  $type
     * @param  string  $defaultLink
     * @param  string  $containerClass
     */
    public function __construct($items = null, $type = '', $defaultLink = '#', $containerClass = '')
    {
        // اگر $items null باشد، یک collection خالی جایگزین می‌کنیم
        $this->items = $items ?? collect();
        $this->type = $type;
        $this->defaultLink = $defaultLink;
        $this->containerClass = $containerClass;
    }

    public function render()
    {
        return view('components.swiper_container');
    }
}
