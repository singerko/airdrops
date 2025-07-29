<?php
// app/Http/Controllers/Admin/ProjectController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\AirdropCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::with('category');

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        $projects = $query->orderBy('created_at', 'desc')->paginate(20);
        $categories = AirdropCategory::active()->get();

        return view('admin.projects.index', compact('projects', 'categories'));
    }

    public function create()
    {
        $categories = AirdropCategory::active()->get();
        
        return view('admin.projects.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:1024',
            'website' => 'nullable|url',
            'twitter' => 'nullable|url',
            'discord' => 'nullable|url',
            'telegram' => 'nullable|url',
            'github' => 'nullable|url',
            'category_id' => 'nullable|exists:airdrop_categories,id',
            'is_verified' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')
                ->store('projects', 'public');
        }

        $project = Project::create($validated);

        return redirect()->route('admin.projects.show', $project)
            ->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        $project->load(['category', 'airdrops.blockchain']);
        
        return view('admin.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $categories = AirdropCategory::active()->get();
        
        return view('admin.projects.edit', compact('project', 'categories'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:1024',
            'website' => 'nullable|url',
            'twitter' => 'nullable|url',
            'discord' => 'nullable|url',
            'telegram' => 'nullable|url',
            'github' => 'nullable|url',
            'category_id' => 'nullable|exists:airdrop_categories,id',
            'is_verified' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($project->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        if ($request->hasFile('logo')) {
            if ($project->logo) {
                Storage::disk('public')->delete($project->logo);
            }
            $validated['logo'] = $request->file('logo')
                ->store('projects', 'public');
        }

        $project->update($validated);

        return redirect()->route('admin.projects.show', $project)
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        if ($project->airdrops()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete project with existing airdrops.');
        }

        if ($project->logo) {
            Storage::disk('public')->delete($project->logo);
        }

        $project->delete();

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}
