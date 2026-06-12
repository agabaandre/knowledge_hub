<?php

namespace App\Support;

use App\Models\Author;
use App\Models\Country;
use App\Models\GeoCoverage;
use App\Models\PublicationCountry;
use App\Models\PublicationTag;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;

final class PublicationSearchQuery
{
    /** @var list<string> */
    private const STOP_WORDS = [
        'a', 'an', 'the', 'and', 'or', 'of', 'in', 'on', 'for', 'to', 'with', 'by', 'from', 'at',
        'is', 'are', 'was', 'were', 'be', 'been', 'about', 'into', 'over', 'under', 'between',
    ];

    public static function normalizeTerm(?string $term): string
    {
        $term = trim(strip_tags((string) $term));
        if ($term === '') {
            return '';
        }

        $term = preg_replace('/[\x{00A0}\x{2007}\x{202F}]+/u', ' ', $term) ?? $term;
        $term = preg_replace('/\s+/u', ' ', $term) ?? $term;

        return trim($term);
    }

    /**
     * @return list<string>
     */
    public static function significantWords(string $term): array
    {
        $term = self::normalizeTerm($term);
        if ($term === '') {
            return [];
        }

        $rawWords = preg_split('/\s+/u', $term, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $words = [];

        foreach ($rawWords as $word) {
            $word = trim($word, ".,;:!?\"'()[]{}");
            if ($word === '' || mb_strlen($word) < 2) {
                continue;
            }

            if (in_array(mb_strtolower($word), self::STOP_WORDS, true)) {
                continue;
            }

            $words[] = $word;
        }

        if ($words !== []) {
            return array_values(array_unique($words));
        }

        foreach ($rawWords as $word) {
            $word = trim($word, ".,;:!?\"'()[]{}");
            if ($word !== '' && mb_strlen($word) >= 2) {
                $words[] = $word;
            }
        }

        return array_values(array_unique($words));
    }

    /**
     * @param  Builder|\Illuminate\Database\Query\Builder  $query
     */
    public static function apply($query, ?string $term): void
    {
        $term = self::normalizeTerm($term);
        if ($term === '') {
            return;
        }

        $safeTerm = addcslashes($term, '%_\\');
        $words = self::significantWords($term);

        $query->where(function ($q) use ($safeTerm, $words) {
            $q->where(function ($phraseQuery) use ($safeTerm) {
                self::applyTokenMatch($phraseQuery, $safeTerm);
            });

            if (count($words) > 1) {
                $q->orWhere(function ($allWordsQuery) use ($words) {
                    foreach ($words as $word) {
                        $safeWord = addcslashes($word, '%_\\');
                        $allWordsQuery->where(function ($wordQuery) use ($safeWord) {
                            self::applyTokenMatch($wordQuery, $safeWord);
                        });
                    }
                });
            }
        });
    }

    /**
     * @param  Builder|\Illuminate\Database\Query\Builder  $query
     */
    private static function applyTokenMatch($query, string $safeToken): void
    {
        $query->where('title', 'like', '%'.$safeToken.'%')
            ->orWhere('description', 'like', '%'.$safeToken.'%')
            ->orWhere('author_affiliation', 'like', '%'.$safeToken.'%')
            ->orWhere('associated_authors', 'like', '%'.$safeToken.'%')
            ->orWhereIn('author_id', Author::query()->where('name', 'like', '%'.$safeToken.'%')->pluck('id'))
            ->orWhereHas('author', function ($authorQuery) use ($safeToken) {
                $authorQuery->where('name', 'like', '%'.$safeToken.'%');
            })
            ->orWhereHas('country', function ($countryQuery) use ($safeToken) {
                $countryQuery->where('name', 'like', '%'.$safeToken.'%');
            })
            ->orWhereHas('countries', function ($countryQuery) use ($safeToken) {
                $countryQuery->where('name', 'like', '%'.$safeToken.'%');
            })
            ->orWhereIn('id', PublicationCountry::query()
                ->whereIn('country_id', Country::query()
                    ->whereHas('region', function ($regionQuery) use ($safeToken) {
                        $regionQuery->where('region_name', 'like', '%'.$safeToken.'%');
                    })
                    ->pluck('id'))
                ->pluck('publication_id'))
            ->orWhereHas('sub_theme', function ($subThemeQuery) use ($safeToken) {
                $subThemeQuery->where('description', 'like', '%'.$safeToken.'%')
                    ->orWhereHas('theme', function ($themeQuery) use ($safeToken) {
                        $themeQuery->where('description', 'like', '%'.$safeToken.'%');
                    });
            })
            ->orWhereHas('data_category', function ($categoryQuery) use ($safeToken) {
                $categoryQuery->where('category_name', 'like', '%'.$safeToken.'%');
            })
            ->orWhereIn('id', PublicationTag::query()
                ->whereIn('tag_id', Tag::query()->where('tag_text', 'like', '%'.$safeToken.'%')->pluck('id'))
                ->pluck('publication_id'));

        if (function_exists('states_enabled') && states_enabled()) {
            $query->orWhereIn('geographical_coverage_id', GeoCoverage::query()
                ->where('name', 'like', '%'.$safeToken.'%')
                ->pluck('id'));
        }
    }
}
