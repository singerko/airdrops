<?php
// app/Http/Controllers/ProjectController.php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\AirdropCategory;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::active()
            ->with(['category', 'airdrops'])
            ->withCount(['airdrops']);

        // Search
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        // Filter by category
        if ($request->category) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by verification status
        if ($request->verified) {
            $query->verified();
        }

        // Sorting
        $sortBy = $request->sort_by ?? 'latest';
        switch ($sortBy) {
            case 'popular':
                $query->orderBy('rating', 'desc');
                break;
            case 'alphabetical':
                $query->orderBy('name');
                break;
            case 'most_airdrops':
                $query->orderBy('airdrops_count', 'desc');
                break;
            default:
                $query->latest();
        }

        $projects = $query->paginate(12)->withQueryString();
        $categories = AirdropCategory::active()->ordered()->get();

        return view('projects.index', compact('projects', 'categories'));
    }

    public function show($slug)
    {
        $project = Project::active()
            ->with([
                'category',
                'airdrops' => function ($query) {
                    $query->published()
                          ->with('blockchain')
                          ->latest('published_at');
                }
            ])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('projects.show', compact('project'));
    }
}

