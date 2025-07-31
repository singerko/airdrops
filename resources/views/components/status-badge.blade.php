<!-- resources/views/components/status-badge.blade.php -->
<span class="{{ $component->getBadgeClasses() }}">
    {{ ucfirst($status) }}
</span>
