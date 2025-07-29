<?php
// app/View/Components/DataTable.php

namespace App\View\Components;

use Illuminate\View\Component;

class DataTable extends Component
{
    public array $columns;
    public $rows;
    public array $actions;
    public bool $sortable;

    public function __construct(
        array $columns, 
        $rows, 
        array $actions = [],
        bool $sortable = true
    ) {
        $this->columns = $columns;
        $this->rows = $rows;
        $this->actions = $actions;
        $this->sortable = $sortable;
    }

    public function render()
    {
        return view('components.data-table');
    }
}
