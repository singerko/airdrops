<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'site_name' => config('app.name'),
            'site_description' => config('app.description'),
            'admin_email' => config('mail.from.address'),
            'items_per_page' => config('app.items_per_page', 20),
            'cache_duration' => config('cache.duration', 3600),
            'enable_registration' => config('app.enable_registration', true),
            'enable_social_login' => config('services.enable_social_login', true),
            'enable_wallet_login' => config('services.enable_wallet_login', true),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string|max:500',
            'admin_email' => 'required|email',
            'items_per_page' => 'required|integer|min:10|max:100',
            'cache_duration' => 'required|integer|min:60|max:86400',
            'enable_registration' => 'boolean',
            'enable_social_login' => 'boolean',
            'enable_wallet_login' => 'boolean',
        ]);

        // Update .env file
        $this->updateEnvFile($validated);

        // Clear cache
        Cache::flush();

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated successfully.');
    }

    private function updateEnvFile(array $values)
    {
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        $mappings = [
            'site_name' => 'APP_NAME',
            'admin_email' => 'MAIL_FROM_ADDRESS',
        ];

        foreach ($mappings as $key => $envKey) {
            if (isset($values[$key])) {
                $envContent = preg_replace(
                    "/{$envKey}=.*/",
                    "{$envKey}=\"{$values[$key]}\"",
                    $envContent
                );
            }
        }

        file_put_contents($envPath, $envContent);
    }
}