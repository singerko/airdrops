<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AirdropHeader extends Component
{
    public $airdrop;
    public $title;
    public $description;
    public $breadcrumbs;

    /**
     * Create a new component instance.
     */
    public function __construct($airdrop = null, $title = null, $description = null, $breadcrumbs = [])
    {
        $this->airdrop = $airdrop;
        $this->title = $title ?? ($airdrop->name ?? 'Airdrop Details');
        $this->description = $description ?? ($airdrop->description ?? '');
        $this->breadcrumbs = $breadcrumbs;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.airdrop-header');
    }
}
