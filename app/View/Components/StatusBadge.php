<?php
// app/View/Components/StatusBadge.php

namespace App\View\Components;

use Illuminate\View\Component;

class StatusBadge extends Component
{
    public string $status;
    public string $size;

    public function __construct(string $status, string $size = 'sm')
    {
        $this->status = $status;
        $this->size = $size;
    }

    public function render()
    {
        return view('components.status-badge');
    }

    public function getBadgeClasses()
    {
        $sizeClasses = match($this->size) {
            'xs' => 'px-2 py-0.5 text-xs',
            'sm' => 'px-2.5 py-0.5 text-xs',
            'md' => 'px-3 py-1 text-sm',
            'lg' => 'px-4 py-1 text-base',
            default => 'px-2.5 py-0.5 text-xs',
        };

        $colorClasses = match($this->status) {
            'active' => 'bg-green-500 text-white',
            'upcoming' => 'bg-blue-500 text-white',
            'ended' => 'bg-gray-500 text-white',
            'cancelled' => 'bg-red-500 text-white',
            'draft' => 'bg-yellow-500 text-white',
            default => 'bg-gray-400 text-white',
        };

        return "inline-flex items-center font-medium rounded-full {$sizeClasses} {$colorClasses}";
    }
}
