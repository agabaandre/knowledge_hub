<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteLanguage;
use App\Services\ChatGPTService;
use App\Services\UiTranslationService;
use App\Support\AiConfig;
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

        $wantsJson = $request->ajax()
            || $request->wantsJson()
            || $request->expectsJson()
            || $request->header('X-Requested-With') === 'XMLHttpRequest';

        $translations = $validated['translations'];
        // Blank out values still identical to English so packaged locale files are not overridden.
        if ($validated['locale'] !== 'en') {
            $english = $this->uiTranslations->loadEnglishGroup($validated['group']);
            foreach ($translations as $key => $value) {
                if (! is_string($value)) {
                    continue;
                }
                $en = $english[$key] ?? null;
                if (is_string($en) && trim($value) === $en) {
                    $translations[$key] = '';
                }
            }
        }

        try {
            $this->uiTranslations->saveGroup(
                $validated['locale'],
                $validated['group'],
                $translations
            );
        } catch (\Throwable $e) {
            report($e);

            if ($wantsJson) {
                return response()->json([
                    'ok' => false,
                    'message' => __('admin_language_management.save_failed').' '.$e->getMessage(),
                ], 500);
            }

            return back()
                ->withInput()
                ->with('alert-danger', __('admin_language_management.save_failed').' '.$e->getMessage());
        }

        if ($wantsJson) {
            return response()->json([
                'ok' => true,
                'message' => __('admin_language_management.saved'),
                'locale' => $validated['locale'],
                'group' => $validated['group'],
                'saved' => count($validated['translations']),
            ]);
        }

        return redirect()
            ->route('admin.language-management.index', [
                'locale' => $validated['locale'],
                'group' => $validated['group'],
            ])
            ->with('alert-success', __('admin_language_management.saved'));
    }

    /**
     * AI-assisted fill: translate English source strings into the selected locale (review then Save).
     * Accepts optional `keys` so the browser can send small chunks (avoids PHP max_execution_time kills).
     */
    public function aiTranslate(Request $request, ChatGPTService $chatGpt)
    {
        // Web SAPI defaults to 30s; a full nav group needs multiple OpenAI round-trips.
        if (function_exists('set_time_limit')) {
            @set_time_limit(180);
        }

        $groups = array_keys($this->uiTranslations->groups());
        $locales = $this->uiTranslations->supportedLocales();

        $validated = $request->validate([
            'locale' => ['required', 'string', Rule::in($locales)],
            'group' => ['required', 'string', Rule::in($groups)],
            // Keep request payloads small so each AI round-trip stays under proxy/PHP timeouts.
            'keys' => ['sometimes', 'array', 'max:12'],
            'keys.*' => ['string', 'max:190'],
        ]);

        if ($validated['locale'] === 'en') {
            return response()->json([
                'ok' => false,
                'message' => 'Select a locale other than English to translate into.',
            ], 422);
        }

        if (AiConfig::resolveChatProviderForFeature('translation') === null) {
            return response()->json([
                'ok' => false,
                'message' => 'No AI provider is configured for UI translation. Set an API key under Admin → Settings → AI integrations (or OPEN_API_KEY).',
            ], 503);
        }

        $english = $this->uiTranslations->loadEnglishGroup($validated['group']);
        if ($english === []) {
            return response()->json([
                'ok' => false,
                'message' => 'No English strings found for this section.',
            ], 404);
        }

        $requestedKeys = $validated['keys'] ?? null;
        if (is_array($requestedKeys) && $requestedKeys !== []) {
            $subset = [];
            foreach ($requestedKeys as $key) {
                if (array_key_exists($key, $english)) {
                    $subset[$key] = $english[$key];
                }
            }
            $english = $subset;
        }

        if ($english === []) {
            return response()->json([
                'ok' => false,
                'message' => 'None of the requested keys exist in this section.',
            ], 404);
        }

        $label = $this->localeDisplayName($validated['locale']);
        $result = $chatGpt->translateUiStringBatch($label, $english);

        if (! ($result['ok'] ?? false)) {
            return response()->json([
                'ok' => false,
                'message' => $result['error'] ?? 'Translation failed.',
            ], 502);
        }

        return response()->json([
            'ok' => true,
            'translations' => $result['translations'] ?? [],
            'translated' => count($result['translations'] ?? []),
            'requested' => count($english),
        ]);
    }

    /**
     * Build an async work queue for bulk AI translate (all sections and/or all locales).
     * Client walks jobs in small AI chunks so the browser stays responsive.
     */
    public function bulkPlan(Request $request)
    {
        $groupsMap = $this->uiTranslations->groups();
        $groupKeys = array_keys($groupsMap);
        $locales = $this->uiTranslations->supportedLocales();

        $validated = $request->validate([
            'scope' => ['required', 'string', Rule::in(['sections', 'all_languages'])],
            'locale' => ['nullable', 'string', Rule::in($locales)],
            'only_missing' => ['sometimes', 'boolean'],
        ]);

        $onlyMissing = $request->boolean('only_missing', true);
        $targetLocales = [];

        if ($validated['scope'] === 'sections') {
            $locale = (string) ($validated['locale'] ?? '');
            if ($locale === '' || $locale === 'en') {
                return response()->json([
                    'ok' => false,
                    'message' => __('admin_language_management.select_non_english_ai'),
                ], 422);
            }
            $targetLocales = [$locale];
        } else {
            $targetLocales = array_values(array_filter(
                $locales,
                static fn (string $code): bool => $code !== 'en'
            ));
        }

        if ($targetLocales === []) {
            return response()->json([
                'ok' => false,
                'message' => __('admin_language_management.bulk_no_locales'),
            ], 422);
        }

        $jobs = [];
        $totalKeys = 0;
        foreach ($targetLocales as $locale) {
            foreach ($groupKeys as $group) {
                $english = $this->uiTranslations->loadEnglishGroup($group);
                if ($english === []) {
                    continue;
                }

                $keys = $onlyMissing
                    ? array_keys($this->uiTranslations->missingKeysForLocale($locale, $group))
                    : array_keys($english);

                if ($keys === []) {
                    continue;
                }

                $jobs[] = [
                    'locale' => $locale,
                    'group' => $group,
                    'group_label' => $groupsMap[$group] ?? $group,
                    'keys' => $keys,
                    'key_count' => count($keys),
                ];
                $totalKeys += count($keys);
            }
        }

        return response()->json([
            'ok' => true,
            'scope' => $validated['scope'],
            'only_missing' => $onlyMissing,
            'jobs' => $jobs,
            'job_count' => count($jobs),
            'total_keys' => $totalKeys,
            'locales' => $targetLocales,
            'groups' => $groupKeys,
        ]);
    }

    /**
     * Translate a small key chunk with AI and merge-save into storage (for bulk async runs).
     */
    public function aiTranslateSave(Request $request, ChatGPTService $chatGpt)
    {
        if (function_exists('set_time_limit')) {
            @set_time_limit(180);
        }

        $groups = array_keys($this->uiTranslations->groups());
        $locales = $this->uiTranslations->supportedLocales();

        $validated = $request->validate([
            'locale' => ['required', 'string', Rule::in($locales)],
            'group' => ['required', 'string', Rule::in($groups)],
            // Small chunks keep bulk AI translate responsive and avoid gateway timeouts.
            'keys' => ['required', 'array', 'min:1', 'max:12'],
            'keys.*' => ['string', 'max:190'],
            'only_missing' => ['sometimes', 'boolean'],
        ]);

        if ($validated['locale'] === 'en') {
            return response()->json([
                'ok' => false,
                'message' => __('admin_language_management.select_non_english_ai'),
            ], 422);
        }

        if (AiConfig::resolveChatProviderForFeature('translation') === null) {
            return response()->json([
                'ok' => false,
                'message' => 'No AI provider is configured for UI translation. Set an API key under Admin → Settings → AI integrations (or OPEN_API_KEY).',
            ], 503);
        }

        $onlyMissing = $request->boolean('only_missing', true);
        if ($onlyMissing) {
            $english = $this->uiTranslations->missingKeysForLocale(
                $validated['locale'],
                $validated['group'],
                $validated['keys']
            );
        } else {
            $all = $this->uiTranslations->loadEnglishGroup($validated['group']);
            $english = [];
            foreach ($validated['keys'] as $key) {
                if (array_key_exists($key, $all)) {
                    $english[$key] = $all[$key];
                }
            }
        }

        if ($english === []) {
            return response()->json([
                'ok' => true,
                'skipped' => true,
                'translated' => 0,
                'saved' => 0,
                'requested' => count($validated['keys']),
                'message' => __('admin_language_management.bulk_chunk_skipped'),
            ]);
        }

        $label = $this->localeDisplayName($validated['locale']);
        $result = $chatGpt->translateUiStringBatch($label, $english);

        if (! ($result['ok'] ?? false)) {
            return response()->json([
                'ok' => false,
                'message' => $result['error'] ?? __('admin_language_management.ai_failed'),
            ], 502);
        }

        $translations = $result['translations'] ?? [];
        // Never persist English fallbacks — they override packaged locale files online.
        $translations = $this->uiTranslations->rejectEnglishIdentical(
            $validated['group'],
            is_array($translations) ? $translations : []
        );

        if ($translations === []) {
            return response()->json([
                'ok' => true,
                'skipped' => true,
                'translated' => 0,
                'saved' => 0,
                'requested' => count($english),
                'message' => __('admin_language_management.bulk_chunk_skipped'),
            ]);
        }

        try {
            $saved = $this->uiTranslations->mergeGroupTranslations(
                $validated['locale'],
                $validated['group'],
                $translations,
                true
            );
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'ok' => false,
                'message' => __('admin_language_management.save_failed').' '.$e->getMessage(),
            ], 500);
        }

        return response()->json([
            'ok' => true,
            'skipped' => false,
            'translations' => $translations,
            'translated' => count($translations),
            'saved' => $saved,
            'requested' => count($english),
            'locale' => $validated['locale'],
            'group' => $validated['group'],
        ]);
    }

    /**
     * Remove storage overrides that are still English so packaged locale files show again.
     */
    public function clearEnglishOverrides(Request $request)
    {
        $groups = array_keys($this->uiTranslations->groups());
        $locales = $this->uiTranslations->supportedLocales();

        $validated = $request->validate([
            'locale' => ['required', 'string', Rule::in($locales)],
            'scope' => ['sometimes', 'string', Rule::in(['group', 'locale'])],
            'group' => ['nullable', 'string', Rule::in($groups)],
        ]);

        if ($validated['locale'] === 'en') {
            return response()->json([
                'ok' => false,
                'message' => __('admin_language_management.select_non_english_ai'),
            ], 422);
        }

        $scope = $validated['scope'] ?? 'locale';
        $group = $scope === 'group' ? ($validated['group'] ?? null) : null;

        try {
            $result = $this->uiTranslations->clearEnglishOverrides(
                $validated['locale'],
                $group
            );
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'ok' => false,
                'message' => __('admin_language_management.clear_english_failed').' '.$e->getMessage(),
            ], 500);
        }

        return response()->json([
            'ok' => true,
            'locale' => $validated['locale'],
            'group' => $group,
            'cleared' => $result['cleared'],
            'files_updated' => $result['files_updated'],
            'files_deleted' => $result['files_deleted'],
            'message' => __('admin_language_management.clear_english_done', [
                'cleared' => $result['cleared'],
            ]),
        ]);
    }

    /**
     * Fill the editor from English (empty keys only, unless overwrite=1). Review then Save.
     */
    public function copyEnglish(Request $request)
    {
        $groups = array_keys($this->uiTranslations->groups());
        $locales = $this->uiTranslations->supportedLocales();

        $validated = $request->validate([
            'locale' => ['required', 'string', Rule::in($locales)],
            'group' => ['required', 'string', Rule::in($groups)],
            'overwrite' => ['sometimes', 'boolean'],
        ]);

        if ($validated['locale'] === 'en') {
            return response()->json([
                'ok' => false,
                'message' => 'Select a locale other than English to copy into.',
            ], 422);
        }

        $onlyEmpty = ! $request->boolean('overwrite');
        $existing = $this->uiTranslations->loadRawLocaleGroup($validated['locale'], $validated['group']);
        $translations = $this->uiTranslations->copyEnglishPayload(
            $validated['locale'],
            $validated['group'],
            $existing,
            $onlyEmpty
        );

        return response()->json([
            'ok' => true,
            'translations' => $translations,
        ]);
    }

    private function localeDisplayName(string $locale): string
    {
        $map = SiteLanguage::selectorMap();
        if (isset($map[$locale]['name']) && $map[$locale]['name'] !== '') {
            return (string) $map[$locale]['name'];
        }

        return strtoupper($locale);
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
        // Prefer Configure → Default language (e.g. FR on Gabon), not the first sorted locale (often EN).
        $systemDefault = SiteLanguage::systemDefaultLocale();
        $defaultLocale = in_array($systemDefault, $locales, true)
            ? $systemDefault
            : (string) ($locales[0] ?? 'en');

        $locale = (string) $request->query('locale', $defaultLocale);
        if (! in_array($locale, $locales, true)) {
            $locale = $defaultLocale;
        }

        $firstGroup = array_key_first($groups) ?: 'frontend_nav';
        $group = (string) $request->query('group', $firstGroup);
        if (! array_key_exists($group, $groups)) {
            $group = $firstGroup;
        }

        return [$locale, $group];
    }
}
