<!-- resources/views/layouts/app.blade.php -->
@extends('layouts.base')

@section('body')
<div id="app">
    <!-- Navigation -->
    <x-navigation />

    <!-- Flash Messages -->
    <x-flash-messages />

    <!-- Main Content -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- Footer -->
    <x-footer />
</div>
@endsection
