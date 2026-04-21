<?php

namespace App\Support;

use App\Models\Author;
use App\Models\Country;
use App\Models\DataCategory;
use App\Models\PublicationCategory;
use App\Models\PublicationType;
use App\Models\Region;
use App\Models\Tag;
use App\Models\ThemeticArea;
use App\Repositories\PublicationsRepository;
use Illuminate\Http\Request;

/**
 * JSON shape for mobile/apps to render the same filter groups as web records/search
 * (sidebar refine facets + advanced search fields from search_fields.blade.php).
 */
final class RecordsSearchFilterSchema
{
    public static function build(Request $request, PublicationsRepository $publicationsRepo): array
    {
        $minutes = (int) env('CACHE_EXPIRY_DURATION_MINUTES', 60 * 24);

        $dataCategories = cache()->remember('categories', $minutes, function () {
            return DataCategory::with(['sub_categories' => function ($q) {
                $q->orderBy('sub_catgeory_name', 'asc');
            }])->where('show_on_menu', 1)->orderBy('category_name', 'asc')->get();
        });

        $facetCategories = $dataCategories->filter(function ($c) {
            return empty($c->is_special);
        });

        $fileTypes = cache()->remember('file_types', $minutes, function () {
            return PublicationType::query()->orderBy('name')->get();
        });

        $fileCategories = cache()->remember('file_categories', $minutes, function () {
            return PublicationCategory::parentOnly()->orderBy('category_name', 'asc')->get();
        });

        $groups = [];

        $refineControls = [];
        if ($fileTypes->isNotEmpty()) {
            $refineControls[] = [
                'id' => 'file_type',
                'param' => 'file_type_id',
                'label' => 'File type',
                'type' => 'checkbox',
                'multi' => true,
                'options' => $fileTypes->map(fn ($t) => [
                    'value' => (int) $t->id,
                    'label' => (string) $t->name,
                ])->values()->all(),
            ];
        }
        if ($facetCategories->isNotEmpty()) {
            $refineControls[] = [
                'id' => 'data_category',
                'param' => 'data_category_id',
                'label' => 'Category',
                'type' => 'checkbox',
                'multi' => true,
                'options' => $facetCategories->map(fn ($c) => [
                    'value' => (int) $c->id,
                    'label' => (string) $c->category_name,
                ])->values()->all(),
            ];
        }
        if ($fileCategories->isNotEmpty()) {
            $refineControls[] = [
                'id' => 'file_category',
                'param' => 'file_category_id',
                'label' => 'Sub category',
                'type' => 'checkbox',
                'multi' => true,
                'options' => $fileCategories->map(fn ($c) => [
                    'value' => (int) $c->id,
                    'label' => (string) $c->category_name,
                ])->values()->all(),
            ];
        }
        if ($refineControls !== []) {
            $groups[] = [
                'group_id' => 'refine_results',
                'title' => 'Refine results',
                'controls' => $refineControls,
            ];
        }

        $tags = Tag::popularByEngagement(20);
        if ($tags->isNotEmpty()) {
            $groups[] = [
                'group_id' => 'popular_tags',
                'title' => 'Popular Tags',
                'controls' => [[
                    'id' => 'tag',
                    'param' => 'tag',
                    'label' => 'Tag',
                    'type' => 'pill',
                    'multi' => false,
                    'options' => $tags->map(fn ($tag) => [
                        'value' => (int) $tag->id,
                        'label' => (string) $tag->tag_text,
                    ])->values()->all(),
                ]],
            ];
        }

        $groups[] = [
            'group_id' => 'keyword',
            'title' => 'Search',
            'controls' => [[
                'id' => 'term',
                'param' => 'term',
                'label' => 'Keywords',
                'type' => 'text',
                'multi' => false,
            ]],
        ];

        $advanced = [
            'group_id' => 'advanced_search',
            'title' => 'Advance your Search With Filters',
            'controls' => [],
        ];

        if (function_exists('states_enabled') && states_enabled()) {
            $advanced['controls'][] = [
                'id' => 'rcc',
                'param' => 'rcc',
                'label' => 'Region',
                'type' => 'select',
                'multi' => false,
                'allow_all' => true,
                'all_value' => 'all',
                'options' => Region::query()->orderBy('region_name')->get()->map(fn ($r) => [
                    'value' => (string) $r->id,
                    'label' => (string) $r->region_name,
                ])->values()->all(),
            ];
            $advanced['controls'][] = [
                'id' => 'country_id',
                'param' => 'country_id',
                'label' => 'Member State',
                'type' => 'select',
                'multi' => false,
                'allow_all' => true,
                'all_value' => '',
                'options' => Country::query()->where('national', 'national')->orderBy('name')->get()->map(fn ($c) => [
                    'value' => (int) $c->id,
                    'label' => (string) $c->name,
                ])->values()->all(),
            ];
        }

        $advanced['controls'][] = [
            'id' => 'thematic_area_id',
            'param' => 'thematic_area_id',
            'aliases' => ['theme'],
            'label' => 'Thematic Area',
            'type' => 'select',
            'multi' => false,
            'allow_all' => true,
            'all_value' => '',
            'options' => ThemeticArea::query()
                ->orderBy('description', 'asc')
                ->orderBy('id', 'asc')
                ->get()
                ->map(fn ($t) => [
                'value' => (int) $t->id,
                'label' => (string) $t->description,
            ])->values()->all(),
        ];

        $themeId = (int) ($request->input('thematic_area_id') ?: $request->input('theme'));
        $subOptions = [];
        if ($themeId > 0) {
            $subReq = clone $request;
            $subReq->merge(['thematic_area_id' => $themeId]);
            $subOptions = $publicationsRepo->get_subthemes($subReq)->map(fn ($s) => [
                'value' => (int) $s->id,
                'label' => (string) $s->description,
            ])->values()->all();
        }

        $advanced['controls'][] = [
            'id' => 'sub_thematic_area_id',
            'param' => 'sub_thematic_area_id',
            'aliases' => ['subtheme'],
            'label' => 'Sub-Thematic Area',
            'type' => 'select',
            'multi' => false,
            'allow_all' => true,
            'all_value' => '',
            'depends_on' => 'thematic_area_id',
            'options' => $subOptions,
        ];

        $advanced['controls'][] = [
            'id' => 'file_category_select',
            'param' => 'file_category_id',
            'label' => 'Sub Category',
            'type' => 'select',
            'multi' => false,
            'allow_all' => true,
            'all_value' => '',
            'options' => $fileCategories->map(fn ($c) => [
                'value' => (int) $c->id,
                'label' => (string) $c->category_name,
            ])->values()->all(),
        ];

        $advanced['controls'][] = [
            'id' => 'data_category_select',
            'param' => 'data_category_id',
            'aliases' => ['category'],
            'label' => 'Category',
            'type' => 'select',
            'multi' => false,
            'allow_all' => true,
            'all_value' => '',
            'options' => $facetCategories->map(fn ($c) => [
                'value' => (int) $c->id,
                'label' => (string) $c->category_name,
            ])->values()->all(),
        ];

        if (function_exists('states_enabled') && states_enabled()) {
            $advanced['controls'][] = [
                'id' => 'author_id',
                'param' => 'author_id',
                'aliases' => ['author'],
                'label' => 'Source',
                'type' => 'select',
                'multi' => false,
                'allow_all' => true,
                'all_value' => '',
                'options' => Author::query()->orderBy('name')->get(['id', 'name'])->map(fn ($a) => [
                    'value' => (int) $a->id,
                    'label' => (string) $a->name,
                ])->values()->all(),
            ];
            $advanced['controls'][] = [
                'id' => 'file_type_select',
                'param' => 'file_type_id',
                'aliases' => ['file_type'],
                'label' => 'File type',
                'type' => 'select',
                'multi' => false,
                'allow_all' => true,
                'all_value' => '',
                'options' => $fileTypes->map(fn ($t) => [
                    'value' => (int) $t->id,
                    'label' => (string) $t->name,
                ])->values()->all(),
            ];
        } else {
            $advanced['controls'][] = [
                'id' => 'file_type_select',
                'param' => 'file_type_id',
                'aliases' => ['file_type'],
                'label' => 'File type',
                'type' => 'select',
                'multi' => false,
                'allow_all' => true,
                'all_value' => '',
                'options' => $fileTypes->map(fn ($t) => [
                    'value' => (int) $t->id,
                    'label' => (string) $t->name,
                ])->values()->all(),
            ];
        }

        $groups[] = $advanced;

        return [
            'filter_groups' => $groups,
            'supported_query_params' => [
                'term',
                'rcc',
                'country_id',
                'thematic_area_id',
                'theme',
                'sub_thematic_area_id',
                'subtheme',
                'data_category_id',
                'category',
                'file_category_id',
                'file_type_id',
                'file_type',
                'author_id',
                'author',
                'tag',
                'community_id',
                'page',
                'page_size',
                'rows',
                'order_by_visits',
                'skip_random_order',
                'is_featured',
            ],
            'notes' => [
                'Multi-value facets use the same keys as the web UI, e.g. data_category_id[] or repeated file_type_id entries.',
                'When a thematic area is selected, refresh this payload or call GET /api/publications with thematic_area_id to receive updated sub_thematic_area_id options.',
            ],
        ];
    }

    public static function activeFilterSnapshot(Request $request): array
    {
        $out = [];
        $keys = [
            'term', 'rcc', 'country_id', 'thematic_area_id', 'theme', 'sub_thematic_area_id', 'subtheme',
            'data_category_id', 'category', 'file_category_id', 'file_type_id', 'file_type',
            'author_id', 'author', 'tag', 'community_id', 'page', 'page_size', 'rows',
        ];
        foreach ($keys as $key) {
            if (! $request->has($key)) {
                continue;
            }
            $v = $request->input($key);
            if ($v === null || $v === '' || $v === 'all') {
                continue;
            }
            if (is_array($v)) {
                $filtered = array_values(array_filter($v, fn ($x) => $x !== null && $x !== '' && $x !== 'all'));
                if ($filtered !== []) {
                    $out[$key] = $filtered;
                }
            } else {
                $out[$key] = $v;
            }
        }

        return $out;
    }
}
