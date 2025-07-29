<?php
// app/Helpers/helpers.php

if (!function_exists('format_number')) {
    function format_number($number, $precision = 0)
    {
        if ($number >= 1000000000) {
            return round($number / 1000000000, $precision) . 'B';
        } elseif ($number >= 1000000) {
            return round($number / 1000000, $precision) . 'M';
        } elseif ($number >= 1000) {
            return round($number / 1000, $precision) . 'K';
        }
        
        return number_format($number, $precision);
    }
}

if (!function_exists('time_ago')) {
    function time_ago($datetime)
    {
        $time = time() - strtotime($datetime);
        
        if ($time < 60) return 'just now';
        if ($time < 3600) return floor($time/60) . ' minutes ago';
        if ($time < 86400) return floor($time/3600) . ' hours ago';
        if ($time < 2592000) return floor($time/86400) . ' days ago';
        if ($time < 31536000) return floor($time/2592000) . ' months ago';
        
        return floor($time/31536000) . ' years ago';
    }
}

if (!function_exists('blockchain_icon')) {
    function blockchain_icon($blockchain_slug)
    {
        $icons = [
            'ethereum' => '⟠',
            'solana' => '◎',
            'cosmos' => '⚛',
            'polygon' => '⬟',
            'bsc' => '🔶',
            'arbitrum' => '🔷',
        ];
        
        return $icons[$blockchain_slug] ?? '🔗';
    }
}

if (!function_exists('status_color')) {
    function status_color($status)
    {
        $colors = [
            'draft' => 'gray',
            'upcoming' => 'blue',
            'active' => 'green',
            'ended' => 'gray',
            'cancelled' => 'red',
        ];
        
        return $colors[$status] ?? 'gray';
    }
}

if (!function_exists('truncate_address')) {
    function truncate_address($address, $start = 6, $end = 4)
    {
        if (strlen($address) <= $start + $end) {
            return $address;
        }
        
        return substr($address, 0, $start) . '...' . substr($address, -$end);
    }
}
