<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ResultsHeader extends Component
{
    public $totalResults;
    public $currentPage;
    public $perPage;
    public $viewMode;
    public $sortBy;
    public $sortOrder;
    public $searchQuery;
    public $appliedFilters;
	public $range;
	public $hasFilters;

    /**
     * Create a new component instance.
     */
	public function __construct(
		$totalResults = 0,
		$currentPage = 1,
		$perPage = 12,
		$viewMode = 'grid',
		$sortBy = 'created_at',
		$sortOrder = 'desc',
		$searchQuery = '',
		$appliedFilters = []
	) {
		$this->totalResults = $totalResults;
		$this->currentPage = $currentPage;
		$this->perPage = $perPage;
		$this->viewMode = $viewMode;
		$this->sortBy = $sortBy;
		$this->sortOrder = $sortOrder;
		$this->searchQuery = $searchQuery;
		$this->appliedFilters = $appliedFilters ?? [];
		
		// Predpočítajte hodnoty
		$this->range = $this->getResultsRange();
		$this->hasFilters = $this->hasActiveFilters();
	}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.results-header')->with('component', $this);
    }

    /**
     * Get the range of displayed results
     */
    public function getResultsRange()
    {
        $start = ($this->currentPage - 1) * $this->perPage + 1;
        $end = min($this->currentPage * $this->perPage, $this->totalResults);
        
        return [
            'start' => $start,
            'end' => $end
        ];
    }

    /**
     * Check if there are any active filters
     */
    public function hasActiveFilters()
    {
        return !empty($this->searchQuery) || !empty($this->appliedFilters);
    }
}
