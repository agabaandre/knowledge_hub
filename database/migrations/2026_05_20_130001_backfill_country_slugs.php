<?php

use App\Models\Country;
use App\Support\SeoSlugger;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('country', 'slug')) {
            return;
        }

        Country::query()
            ->where(function ($q) {
                $q->whereNull('slug')->orWhere('slug', '');
            })
            ->orderBy('id')
            ->chunkById(200, function ($rows) {
                foreach ($rows as $country) {
                    \Illuminate\Support\Facades\DB::table('country')
                        ->where('id', $country->id)
                        ->update([
                            'slug' => SeoSlugger::forCountry(
                                (string) ($country->name ?? ''),
                                (int) $country->id
                            ),
                        ]);
                }
            });
    }

    public function down(): void
    {
        // Slugs are regenerated from names if needed; no destructive rollback.
    }
};
