<?php
// app/Http/Controllers/Api/ProjectController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::active()
            ->with(['category'])
            ->withCount(['airdrops']);

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->category) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        $projects = $query->paginate($request->per_page ?? 12);

        return response()->json([
            'data' => $projects->items(),
            'meta' => [
                'current_page' => $projects->currentPage(),
                'per_page' => $projects->perPage(),
                'total' => $projects->total(),
                'last_page' => $projects->lastPage(),
            ],
        ]);
    }

    public function show($slug)
    {
        $project = Project::active()
            ->with(['category', 'airdrops.blockchain'])
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json($project);
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $projects = Project::active()
            ->with(['category'])
            ->where('name', 'like', '%' . $query . '%')
            ->limit(10)
            ->get();

        return response()->json($projects);
    }
}
