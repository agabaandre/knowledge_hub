<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('publication', function (Blueprint $table) {
            if (! Schema::hasColumn('publication', 'content_updated_at')) {
                $table->dateTime('content_updated_at')->nullable()->after('updated_at');
            }
            if (! Schema::hasColumn('publication', 'last_visited_at')) {
                $table->dateTime('last_visited_at')->nullable()->after('content_updated_at');
            }
        });

        if (Schema::hasColumn('publication', 'content_updated_at')) {
            DB::statement('
                UPDATE publication
                SET content_updated_at = COALESCE(date_created, DATE(created_at), created_at)
                WHERE content_updated_at IS NULL
            ');
        }

        if (Schema::hasColumn('publication', 'last_visited_at')) {
            DB::statement("
                UPDATE publication p
                INNER JOIN (
                    SELECT publication_id, MAX(created_at) AS last_visit
                    FROM access_logs
                    WHERE publication_id IS NOT NULL AND publication_id != ''
                    GROUP BY publication_id
                ) al ON CAST(al.publication_id AS UNSIGNED) = p.id
                SET p.last_visited_at = al.last_visit
                WHERE p.last_visited_at IS NULL
            ");

            if (Schema::hasTable('publication_views')) {
                DB::statement('
                    UPDATE publication p
                    INNER JOIN (
                        SELECT publication_id, MAX(updated_at) AS last_view
                        FROM publication_views
                        GROUP BY publication_id
                    ) pv ON pv.publication_id = p.id
                    SET p.last_visited_at = pv.last_view
                    WHERE p.last_visited_at IS NULL
                ');
            }
        }
    }

    public function down(): void
    {
        Schema::table('publication', function (Blueprint $table) {
            if (Schema::hasColumn('publication', 'last_visited_at')) {
                $table->dropColumn('last_visited_at');
            }
            if (Schema::hasColumn('publication', 'content_updated_at')) {
                $table->dropColumn('content_updated_at');
            }
        });
    }
};
