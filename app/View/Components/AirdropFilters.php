<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AirdropFilters extends Component
{
    public $blockchains;
    public $categories;
    public $selectedBlockchains;
    public $selectedCategories;
    public $selectedStatus;
    public $search;
    public $sortBy;
    public $sortOrder;

    /**
     * Create a new component instance.
     */
    public function __construct(
        $selectedBlockchains = [],
        $selectedCategories = [],
        $selectedStatus = null,
        $search = '',
        $sortBy = 'created_at',
        $sortOrder = 'desc'
    ) {
        $this->selectedBlockchains = $selectedBlockchains;
        $this->selectedCategories = $selectedCategories;
        $this->selectedStatus = $selectedStatus;
        $this->search = $search;
        $this->sortBy = $sortBy;
        $this->sortOrder = $sortOrder;
        
        // Safely load filter options without models for now
        try {
            // Load blockchains directly from database
            if (Schema::hasTable('blockchains')) {
                $this->blockchains = DB::table('blockchains')
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get();
            } else {
                $this->blockchains = collect([]);
            }
            
            // Load categories from airdrop_categories table
            if (Schema::hasTable('airdrop_categories')) {
                $this->categories = DB::table('airdrop_categories')
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get();
            } else {
                $this->categories = collect([]);
            }
        } catch (\Exception $e) {
            // Fallback to empty collections if there are any issues
            $this->blockchains = collect([]);
            $this->categories = collect([]);
            
            // Log error for debugging
            \Log::info('AirdropFilters error: ' . $e->getMessage());
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.airdrop-filters');
    }
}
