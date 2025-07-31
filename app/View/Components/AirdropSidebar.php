<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AirdropSidebar extends Component
{
    public $airdrop;

    /**
     * Create a new component instance.
     */
    public function __construct($airdrop = null)
    {
        $this->airdrop = $airdrop;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.airdrop-sidebar');
    }
}
