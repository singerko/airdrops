<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ContentSection extends Component
{
    public $title;
    public $subtitle;
    public $content;
    public $class;

    /**
     * Create a new component instance.
     */
    public function __construct($title = null, $subtitle = null, $content = null, $class = '')
    {
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->content = $content;
        $this->class = $class;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.content-section');
    }
}
