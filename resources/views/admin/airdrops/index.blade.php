<!-- resources/views/admin/airdrops/index.blade.php - Refactored -->
@extends('layouts.admin')

@section('title', 'Manage Airdrops')

@section('content')
<div class="p-6">
    <x-admin-header title="Airdrops" 
                    subtitle="Manage all airdrops in the system"
                    :action="['url' => route('admin.airdrops.create'), 'label' => 'Create Airdrop']" />

    <x-admin-filters />

    <x-data-table :columns="[
        ['field' => 'title', 'label' => 'Airdrop', 'sortable' => true],
        ['field' => 'project.name', 'label' => 'Project'],
        ['field' => 'blockchain.name', 'label' => 'Blockchain'],
        ['field' => 'status', 'label' => 'Status', 'component' => 'status-badge'],
        ['field' => 'views_count', 'label' => 'Views'],
        ['field' => 'created_at', 'label' => 'Created'],
    ]" 
    :rows="$airdrops"
    :actions="[
        [
            'type' => 'link',
            'label' => 'View',
            'url' => fn($row) => route('admin.airdrops.show', $row),
            'class' => 'text-primary-600 hover:text-primary-900'
        ],
        [
            'type' => 'link',
            'label' => 'Edit',
            'url' => fn($row) => route('admin.airdrops.edit', $row),
            'class' => 'text-indigo-600 hover:text-indigo-900'
        ],
        [
            'type' => 'form',
            'label' => 'Delete',
            'url' => fn($row) => route('admin.airdrops.destroy', $row),
            'method' => 'DELETE',
            'class' => 'text-red-600 hover:text-red-900',
            'confirm' => 'Are you sure you want to delete this airdrop?'
        ]
    ]" />

    @if($airdrops->hasPages())
        <div class="mt-6">
            {{ $airdrops->links() }}
        </div>
    @endif
</div>
@endsection
