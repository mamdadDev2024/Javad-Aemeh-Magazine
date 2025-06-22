<?php

namespace App\View\Components;

use App\Models\Section;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Header extends Component
{

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $sections = Section::where("name", "titleHeader")->first();

        return view('components.header', compact('sections'));
    }

}
