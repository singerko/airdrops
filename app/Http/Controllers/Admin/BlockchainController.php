<?php
// app/Http/Controllers/Admin/BlockchainController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blockchain;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BlockchainController extends Controller
{
    public function index(Request $request)
    {
        $query = Blockchain::query();

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $blockchains = $query->orderBy('sort_order')->orderBy('name')->paginate(20);

        return view('admin.blockchains.index', compact('blockchains'));
    }

    public function create()
    {
        return view('admin.blockchains.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:1024',
            'website' => 'nullable|url',
            'explorer_url' => 'nullable|url',
            'token_standard' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')
                ->store('blockchains', 'public');
        }

        $blockchain = Blockchain::create($validated);

        return redirect()->route('admin.blockchains.show', $blockchain)
            ->with('success', 'Blockchain created successfully.');
    }

    public function show(Blockchain $blockchain)
    {
        $blockchain->load('airdrops.project');

        return view('admin.blockchains.show', compact('blockchain'));
    }

    public function edit(Blockchain $blockchain)
    {
        return view('admin.blockchains.edit', compact('blockchain'));
    }

    public function update(Request $request, Blockchain $blockchain)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:1024',
            'website' => 'nullable|url',
            'explorer_url' => 'nullable|url',
            'token_standard' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($blockchain->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        if ($request->hasFile('logo')) {
            if ($blockchain->logo) {
                Storage::disk('public')->delete($blockchain->logo);
            }
            $validated['logo'] = $request->file('logo')
                ->store('blockchains', 'public');
        }

        $blockchain->update($validated);

        return redirect()->route('admin.blockchains.show', $blockchain)
            ->with('success', 'Blockchain updated successfully.');
    }

    public function destroy(Blockchain $blockchain)
    {
        if ($blockchain->airdrops()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete blockchain with existing airdrops.');
        }

        if ($blockchain->logo) {
            Storage::disk('public')->delete($blockchain->logo);
        }

        $blockchain->delete();

        return redirect()->route('admin.blockchains.index')
            ->with('success', 'Blockchain deleted successfully.');
    }
}
