<?php
// app/View/Components/EmptyState.php

namespace App\View\Components;

use Illuminate\View\Component;

class EmptyState extends Component
{
    public string $title;
    public ?string $message;
    public ?array $action;
    public ?string $icon;

    public function __construct(
        string $title,
        ?string $message = null,
        ?array $action = null,
        ?string $icon = null
    ) {
        $this->title = $title;
        $this->message = $message;
        $this->action = $action;
        $this->icon = $icon;
    }

    public function render()
    {
        return view('components.empty-state');
    }
}
