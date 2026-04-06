<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\ApiSetting;
use App\Services\ConnectionTestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\View\View;

class ApiSettingsController extends Controller
{
    public function index(): View
    {
        $settings = ApiSetting::orderBy('integration_name')->get();
        return view('settings.api-settings.index', compact('settings'));
    }

    public function edit(string $integrationName): View
    {
        $setting = ApiSetting::where('integration_name', $integrationName)->firstOrFail();
        return view('settings.api-settings.edit', compact('setting'));
    }

    public function update(Request $request, string $integrationName): RedirectResponse
    {
        $setting = ApiSetting::where('integration_name', $integrationName)->firstOrFail();

        $validated = $request->validate([
            'endpoint_url'    => 'required|url|max:500',
            'sandbox_url'     => 'nullable|url|max:500',
            'consumer_key'    => 'nullable|string|max:1000',
            'consumer_secret' => 'nullable|string|max:1000',
            'mode'            => 'required|in:testing,production',
            'is_active'       => 'boolean',
        ]);

        $data = [
            'endpoint_url' => $validated['endpoint_url'],
            'sandbox_url'  => $validated['sandbox_url'] ?? null,
            'mode'         => $validated['mode'],
            'is_active'    => $request->boolean('is_active'),
        ];

        if (! empty($validated['consumer_key'])) {
            $data['consumer_key_encrypted'] = Crypt::encrypt($validated['consumer_key']);
        }
        if (! empty($validated['consumer_secret'])) {
            $data['consumer_secret_encrypted'] = Crypt::encrypt($validated['consumer_secret']);
        }

        $setting->update($data);

        return redirect()->route('master.api-settings.index')
            ->with('success', "Konfigurasi {$setting->integration_name} berhasil diperbarui.");
    }

    public function toggle(string $integrationName): RedirectResponse
    {
        $setting = ApiSetting::where('integration_name', $integrationName)->firstOrFail();
        $setting->update(['is_active' => ! $setting->is_active]);
        $status = $setting->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('master.api-settings.index')
            ->with('success', "Integrasi {$setting->integration_name} berhasil {$status}.");
    }

    public function testConnection(string $integrationName, ConnectionTestService $connectionTestService): JsonResponse
    {
        $result = $connectionTestService->testConnection($integrationName);

        return response()->json([
            'success'     => $result['success'],
            'status_code' => $result['status_code'],
            'message'     => $result['message'],
            'suggestion'  => $result['suggestion'] ?? null,
        ]);
    }
}
