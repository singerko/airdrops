@if ($paginator->hasPages())
    <nav class="flex justify-center">
        {{ $paginator->links() }}
    </nav>
@endif
