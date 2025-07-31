<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AdminActions extends Component
{
    public $model;
    public $actions;

    /**
     * Create a new component instance.
     */
    public function __construct($model = null, $actions = [])
    {
        $this->model = $model;
        $this->actions = $actions ?: $this->getDefaultActions();
    }

    private function getDefaultActions()
    {
        if (!$this->model) {
            return [];
        }

        $modelName = class_basename($this->model);
        $routePrefix = strtolower($modelName);

        return [
            'edit' => [
                'label' => 'Edit',
                'url' => route("admin.{$routePrefix}.edit", $this->model->id),
                'class' => 'text-blue-600 hover:text-blue-900'
            ],
            'delete' => [
                'label' => 'Delete',
                'url' => route("admin.{$routePrefix}.destroy", $this->model->id),
                'class' => 'text-red-600 hover:text-red-900',
                'confirm' => true
            ]
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin-actions');
    }
}
