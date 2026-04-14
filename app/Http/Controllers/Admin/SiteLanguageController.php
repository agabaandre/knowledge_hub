<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteLanguage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class SiteLanguageController extends Controller
{
    public function index()
    {
        if (! Schema::hasTable('site_languages')) {
            return redirect()->route('admin.configure')
                ->with('alert-danger', 'Run migrations to enable site languages (site_languages table missing).');
        }

        $languages = SiteLanguage::query()->orderBy('sort_order')->orderBy('name')->get();

        return view('admin.site-languages.index', compact('languages'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'locale_code' => strtolower(trim((string) $request->input('locale_code', ''))),
        ]);

        $validated = $request->validate([
            'locale_code' => [
                'required',
                'string',
                'max:32',
                'regex:/^[a-z]{2}([_-][a-zA-Z0-9]+)*$/',
                Rule::unique('site_languages', 'locale_code'),
            ],
            'name' => 'required|string|max:120',
            'google_translate_code' => 'nullable|string|max:32',
            'flag_emoji' => 'nullable|string|max:16',
            'sort_order' => 'nullable|integer|min:0|max:65535',
            'is_active' => 'nullable|boolean',
        ]);

        SiteLanguage::create([
            'locale_code' => $validated['locale_code'],
            'name' => $validated['name'],
            'google_translate_code' => $validated['google_translate_code'] ?: null,
            'flag_emoji' => $validated['flag_emoji'] ?: null,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.site-languages.index')
            ->with('alert-success', 'Language added. Add UI translations under Language management if needed.');
    }

    public function update(Request $request, SiteLanguage $siteLanguage)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'google_translate_code' => 'nullable|string|max:32',
            'flag_emoji' => 'nullable|string|max:16',
            'sort_order' => 'nullable|integer|min:0|max:65535',
            'is_active' => 'nullable|boolean',
        ]);

        $siteLanguage->update([
            'name' => $validated['name'],
            'google_translate_code' => $validated['google_translate_code'] ?: null,
            'flag_emoji' => $validated['flag_emoji'] ?: null,
            'sort_order' => (int) ($validated['sort_order'] ?? $siteLanguage->sort_order),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.site-languages.index')
            ->with('alert-success', 'Language updated.');
    }

    public function destroy(SiteLanguage $siteLanguage)
    {
        if (strtolower($siteLanguage->locale_code) === 'en') {
            return back()->with('alert-danger', 'English (en) cannot be removed.');
        }

        $inUse = User::query()->where('langauge', $siteLanguage->locale_code)->exists();
        if ($inUse) {
            return back()->with('alert-danger', 'Cannot delete: users still have this language in their profile. Deactivate it instead or ask users to switch.');
        }

        $siteLanguage->delete();

        return redirect()->route('admin.site-languages.index')
            ->with('alert-success', 'Language removed.');
    }
}
