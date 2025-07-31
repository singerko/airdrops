<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AirdropPhases extends Component
{
    public $airdrop;
    public $phases;
    public $currentPhase;

    /**
     * Create a new component instance.
     */
    public function __construct($airdrop = null)
    {
        $this->airdrop = $airdrop;
        $this->phases = $airdrop ? $airdrop->phases()->orderBy('sort_order')->get() : collect([]);
        $this->currentPhase = $this->phases->where('is_active', true)->first();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.airdrop-phases');
    }
}
