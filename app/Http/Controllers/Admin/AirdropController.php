<?php
// app/Http/Controllers/Admin/AirdropController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Airdrop;
use App\Models\Project;
use App\Models\Blockchain;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class AirdropController extends Controller
{
    public function index(Request $request)
    {
        $query = Airdrop::with(['project', 'blockchain']);

        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhereHas('project', function ($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->blockchain_id) {
            $query->where('blockchain_id', $request->blockchain_id);
        }

        $airdrops = $query->orderBy('created_at', 'desc')->paginate(20);
        $blockchains = Blockchain::active()->get();

        return view('admin.airdrops.index', compact('airdrops', 'blockchains'));
    }

    public function create()
    {
        $projects = Project::active()->get();
        $blockchains = Blockchain::active()->get();
        
        return view('admin.airdrops.create', compact('projects', 'blockchains'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'blockchain_id' => 'required|exists:blockchains,id',
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'reward_amount' => 'nullable|string',
            'reward_token' => 'nullable|string',
            'estimated_value' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,upcoming,active,ended,cancelled',
            'is_featured' => 'boolean',
            'priority' => 'nullable|integer|min:0|max:100',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'featured_image' => 'nullable|image|max:2048',
            'social_links' => 'nullable|array',
            'video_links' => 'nullable|array',
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        $validated['created_by'] = auth()->id();

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')
                ->store('airdrops', 'public');
        }

        if ($validated['status'] !== 'draft') {
            $validated['published_at'] = now();
        }

        $airdrop = Airdrop::create($validated);

        return redirect()->route('admin.airdrops.show', $airdrop)
            ->with('success', 'Airdrop created successfully.');
    }

    public function show(Airdrop $airdrop)
    {
        $airdrop->load(['project', 'blockchain', 'phases', 'translations.language']);
        
        return view('admin.airdrops.show', compact('airdrop'));
    }

    public function edit(Airdrop $airdrop)
    {
        $projects = Project::active()->get();
        $blockchains = Blockchain::active()->get();
        
        return view('admin.airdrops.edit', compact('airdrop', 'projects', 'blockchains'));
    }

    public function update(Request $request, Airdrop $airdrop)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'blockchain_id' => 'required|exists:blockchains,id',
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'reward_amount' => 'nullable|string',
            'reward_token' => 'nullable|string',
            'estimated_value' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,upcoming,active,ended,cancelled',
            'is_featured' => 'boolean',
            'priority' => 'nullable|integer|min:0|max:100',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'featured_image' => 'nullable|image|max:2048',
            'social_links' => 'nullable|array',
            'video_links' => 'nullable|array',
        ]);

        if ($airdrop->title !== $validated['title']) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $validated['updated_by'] = auth()->id();

        if ($request->hasFile('featured_image')) {
            if ($airdrop->featured_image) {
                Storage::disk('public')->delete($airdrop->featured_image);
            }
            $validated['featured_image'] = $request->file('featured_image')
                ->store('airdrops', 'public');
        }

        if ($airdrop->status === 'draft' && $validated['status'] !== 'draft') {
            $validated['published_at'] = now();
        }

        $airdrop->update($validated);

        return redirect()->route('admin.airdrops.show', $airdrop)
            ->with('success', 'Airdrop updated successfully.');
    }

    public function destroy(Airdrop $airdrop)
    {
        if ($airdrop->featured_image) {
            Storage::disk('public')->delete($airdrop->featured_image);
        }

        $airdrop->delete();

        return redirect()->route('admin.airdrops.index')
            ->with('success', 'Airdrop deleted successfully.');
    }

    public function translate(Request $request, Airdrop $airdrop)
    {
        $request->validate([
            'language_id' => 'required|exists:languages,id',
            'auto_translate' => 'boolean',
        ]);

        $language = Language::findOrFail($request->language_id);

        if ($request->auto_translate && config('services.openai.api_key')) {
            return $this->autoTranslate($airdrop, $language);
        }

        return view('admin.airdrops.translate', compact('airdrop', 'language'));
    }

    private function autoTranslate(Airdrop $airdrop, Language $language)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.openai.api_key'),
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "You are a professional translator. Translate the following airdrop content to {$language->name} language. Return only the translated JSON with keys: title, description, requirements."
                    ],
                    [
                        'role' => 'user',
                        'content' => json_encode([
                            'title' => $airdrop->title,
                            'description' => $airdrop->description,
                            'requirements' => $airdrop->requirements,
                        ])
                    ]
                ],
                'max_tokens' => 2000,
                'temperature' => 0.3,
            ]);

            if ($response->successful()) {
                $translatedContent = json_decode($response->json()['choices'][0]['message']['content'], true);
                
                $airdrop->translations()->updateOrCreate(
                    ['language_id' => $language->id],
                    [
                        'title' => $translatedContent['title'],
                        'description' => $translatedContent['description'],
                        'requirements' => $translatedContent['requirements'],
                        'is_ai_generated' => true,
                        'translated_at' => now(),
                    ]
                );

                return redirect()->route('admin.airdrops.show', $airdrop)
                    ->with('success', "Content translated to {$language->name} successfully.");
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Translation failed: ' . $e->getMessage());
        }

        return redirect()->back()
            ->with('error', 'Translation service is not available.');
    }
}
