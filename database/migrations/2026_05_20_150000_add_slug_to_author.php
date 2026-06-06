<?php

use App\Support\SeoSlugger;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('author') || Schema::hasColumn('author', 'slug')) {
            return;
        }

        Schema::table('author', function (Blueprint $table) {
            $table->string('slug', 191)->nullable()->after('name');
            $table->unique('slug', 'author_slug_unique');
        });

        DB::table('author')
            ->select('id', 'name')
            ->orderBy('id')
            ->chunkById(200, function ($rows) {
                foreach ($rows as $row) {
                    $slug = SeoSlugger::forAuthor((string) ($row->name ?? ''), (int) $row->id);
                    DB::table('author')->where('id', $row->id)->update(['slug' => $slug]);
                }
            });
    }

    public function down(): void
    {
        if (! Schema::hasTable('author') || ! Schema::hasColumn('author', 'slug')) {
            return;
        }

        Schema::table('author', function (Blueprint $table) {
            $table->dropUnique('author_slug_unique');
            $table->dropColumn('slug');
        });
    }
};
