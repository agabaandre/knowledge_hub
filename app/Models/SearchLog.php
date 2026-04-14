<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SearchLog extends Model
{
    protected $fillable = [
        'user_id',
        'term',
        'results_count',
        'request_path',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'results_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Split a search string into normalized tokens for analytics / decision support.
     *
     * @return list<string>
     */
    public static function tokenizeForAnalysis(string $term): array
    {
        $term = trim($term);
        if ($term === '') {
            return [];
        }
        $lower = mb_strtolower($term, 'UTF-8');
        $parts = preg_split('/[\s,;.:\/|+&\-]+/u', $lower) ?: [];
        $out = [];
        foreach ($parts as $p) {
            $p = preg_replace('/[^\p{L}\p{N}\-]+/u', '', $p);
            if ($p !== '' && mb_strlen($p, 'UTF-8') >= 2) {
                $out[] = $p;
            }
        }

        return array_values(array_unique($out));
    }
}
