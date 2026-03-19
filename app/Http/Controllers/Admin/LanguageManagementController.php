<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteLanguage;
use App\Services\UiTranslationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LanguageManagementController extends Controller
{
    public function __construct(
        private UiTranslationService $uiTranslations
    ) {}

    public function index(Request $request)
    {
        $locales = $this->uiTranslations->supportedLocales();
        $groups = $this->uiTranslations->groups();

        $locale = $request->query('locale', $locales[0] ?? 'en');
        if (! in_array($locale, $locales, true)) {
            $locale = 'en';
        }

        $group = $request->query('group', array_key_first($groups) ?: 'frontend_nav');
        if (! array_key_exists($group, $groups)) {
            $group = 'frontend_nav';
        }

        $lines = $this->uiTranslations->loadMergedGroup($locale, $group);
        $english = $this->uiTranslations->loadEnglishGroup($group);

        return view('admin.language-management.index', [
            'locales' => $locales,
            'localeLabels' => SiteLanguage::selectorMap(),
            'groups' => $groups,
            'currentLocale' => $locale,
            'currentGroup' => $group,
            'lines' => $lines,
            'english' => $english,
        ]);
    }

    public function update(Request $request)
    {
        $groups = array_keys($this->uiTranslations->groups());
        $locales = $this->uiTranslations->supportedLocales();

        $validated = $request->validate([
            'locale' => ['required', 'string', Rule::in($locales)],
            'group' => ['required', 'string', Rule::in($groups)],
            'translations' => 'required|array',
            'translations.*' => 'nullable|string|max:5000',
        ]);

        try {
            $this->uiTranslations->saveGroup(
                $validated['locale'],
                $validated['group'],
                $validated['translations']
            );
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('alert-danger', 'Could not save translations: '.$e->getMessage());
        }

        return redirect()
            ->route('admin.language-management.index', [
                'locale' => $validated['locale'],
                'group' => $validated['group'],
            ])
            ->with('alert-success', 'Translations saved. Clear application cache if you use config/view caching.');
    }
}
