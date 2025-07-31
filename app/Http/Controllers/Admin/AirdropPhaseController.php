<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Airdrop;
use App\Models\AirdropPhase;
use Illuminate\Http\Request;

class AirdropPhaseController extends Controller
{
    public function create(Airdrop $airdrop)
    {
        return view('admin.airdrops.phases.create', compact('airdrop'));
    }

    public function store(Request $request, Airdrop $airdrop)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'allocation_percentage' => 'nullable|numeric|min:0|max:100',
            'order' => 'integer|min:0',
        ]);

        $validated['airdrop_id'] = $airdrop->id;
        AirdropPhase::create($validated);

        return redirect()->route('admin.airdrops.show', $airdrop)
            ->with('success', 'Phase created successfully.');
    }

    public function edit(Airdrop $airdrop, AirdropPhase $phase)
    {
        return view('admin.airdrops.phases.edit', compact('airdrop', 'phase'));
    }

    public function update(Request $request, Airdrop $airdrop, AirdropPhase $phase)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'allocation_percentage' => 'nullable|numeric|min:0|max:100',
            'order' => 'integer|min:0',
        ]);

        $phase->update($validated);

        return redirect()->route('admin.airdrops.show', $airdrop)
            ->with('success', 'Phase updated successfully.');
    }

    public function destroy(Airdrop $airdrop, AirdropPhase $phase)
    {
        $phase->delete();

        return redirect()->route('admin.airdrops.show', $airdrop)
            ->with('success', 'Phase deleted successfully.');
    }
}