<?php
// app/View/Components/PageHeader.php

namespace App\View\Components;

use Illuminate\View\Component;

class PageHeader extends Component
{
    public string $title;
    public ?string $subtitle;
    public ?array $action;
    public ?array $breadcrumbs;

    public function __construct(
        string $title,
        ?string $subtitle = null,
        ?array $action = null,
        ?array $breadcrumbs = null
    ) {
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->action = $action;
        $this->breadcrumbs = $breadcrumbs;
    }

    public function render()
    {
        return view('components.page-header');
    }
}
