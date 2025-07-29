<?php
// app/Http/Controllers/Api/CategoryController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AirdropCategory;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = AirdropCategory::active()
            ->ordered()
            ->withCount(['projects'])
            ->get();

        return response()->json($categories);
    }
}
