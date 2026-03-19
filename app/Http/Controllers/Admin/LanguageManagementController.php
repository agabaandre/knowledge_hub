<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteLanguage;
use App\Services\UiTranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
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

        [$locale, $group] = $this->resolveLocaleAndGroup($request, $locales, $groups);

        $lines = $this->uiTranslations->loadMergedGroup($locale, $group);
        $english = $this->uiTranslations->loadEnglishGroup($group);

        return view('admin.language-management.index', [
            'locales' => $locales,
            'localeLabels' => $this->labelsForLocales($locales),
            'groups' => $groups,
            'currentLocale' => $locale,
            'currentGroup' => $group,
            'lines' => $lines,
            'english' => $english,
        ]);
    }

    /**
     * Load translation table HTML for AJAX (locale / section switches).
     */
    public function grid(Request $request)
    {
        $locales = $this->uiTranslations->supportedLocales();
        $groups = $this->uiTranslations->groups();

        [$locale, $group] = $this->resolveLocaleAndGroup($request, $locales, $groups);

        $lines = $this->uiTranslations->loadMergedGroup($locale, $group);
        $english = $this->uiTranslations->loadEnglishGroup($group);

        $html = view('admin.language-management.partials.translation-panel', [
            'currentLocale' => $locale,
            'currentGroup' => $group,
            'groups' => $groups,
            'lines' => $lines,
            'english' => $english,
        ])->render();

        return response()->json([
            'ok' => true,
            'locale' => $locale,
            'group' => $group,
            'groupLabel' => $groups[$group] ?? $group,
            'html' => $html,
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

    /**
     * @param  list<string>  $locales
     * @return array<string, array{name: string, flag: string, code: string, google_code: string}>
     */
    private function labelsForLocales(array $locales): array
    {
        $out = SiteLanguage::selectorMap();

        if (! Schema::hasTable('site_languages')) {
            foreach ($locales as $code) {
                if (! isset($out[$code])) {
                    $out[$code] = [
                        'name' => strtoupper($code),
                        'flag' => '',
                        'code' => $code,
                        'google_code' => $code,
                    ];
                }
            }

            return $out;
        }

        $missing = array_values(array_diff($locales, array_keys($out)));
        if ($missing !== []) {
            $rows = SiteLanguage::query()->whereIn('locale_code', $missing)->get()->keyBy('locale_code');
            foreach ($missing as $code) {
                $row = $rows->get($code);
                $out[$code] = [
                    'name' => $row?->name ?? strtoupper($code),
                    'flag' => $row?->flag_emoji ?? '',
                    'code' => $code,
                    'google_code' => $row?->google_translate_code ?: $code,
                ];
            }
        }

        foreach ($locales as $code) {
            if (! isset($out[$code])) {
                $out[$code] = [
                    'name' => strtoupper($code),
                    'flag' => '',
                    'code' => $code,
                    'google_code' => $code,
                ];
            }
        }

        return $out;
    }

    /**
     * @param  list<string>  $locales
     * @param  array<string, string>  $groups
     * @return array{0: string, 1: string}
     */
    private function resolveLocaleAndGroup(Request $request, array $locales, array $groups): array
    {
        $defaultLocale = (string) ($locales[0] ?? 'en');
        $locale = (string) $request->query('locale', $defaultLocale);
        if (! in_array($locale, $locales, true)) {
            $locale = in_array('en', $locales, true) ? 'en' : $defaultLocale;
        }

        $firstGroup = array_key_first($groups) ?: 'frontend_nav';
        $group = (string) $request->query('group', $firstGroup);
        if (! array_key_exists($group, $groups)) {
            $group = $firstGroup;
        }

        return [$locale, $group];
    }
}
