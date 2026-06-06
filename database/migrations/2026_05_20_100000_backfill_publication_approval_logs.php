<?php

use App\Support\LegacyApprovalLogBackfill;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('publication_approval_logs')) {
            return;
        }

        LegacyApprovalLogBackfill::backfillPublications();
    }

    public function down(): void
    {
        if (! Schema::hasTable('publication_approval_logs')) {
            return;
        }

        DB::table('publication_approval_logs')
            ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.backfilled')) = 'true'")
            ->delete();
    }
};
