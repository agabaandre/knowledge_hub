<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Collapse per-field mail_api_* columns into a single mail_api_config blob
 * (for environments that already ran the older multi-column migration).
 */
return new class extends Migration
{
    private const LEGACY = [
        'mail_api_key' => 'key',
        'mail_api_secret' => 'secret',
        'mail_api_domain' => 'domain',
        'mail_api_region' => 'region',
        'mail_api_base_url' => 'base_url',
        'mail_api_message_stream' => 'message_stream',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('setting')) {
            return;
        }

        $payloads = [];
        if (Schema::hasColumn('setting', 'mail_api_key')) {
            $select = array_merge(['id'], array_keys(self::LEGACY));
            foreach (DB::table('setting')->select($select)->get() as $row) {
                $cfg = [];
                foreach (self::LEGACY as $column => $jsonKey) {
                    $value = $row->{$column} ?? null;
                    if ($value !== null && $value !== '') {
                        $cfg[$jsonKey] = $value;
                    }
                }
                $payloads[$row->id] = $cfg === [] ? null : json_encode($cfg, JSON_UNESCAPED_SLASHES);
            }

            // Drop first so the wide row can accept a single replacement column.
            Schema::table('setting', function (Blueprint $table) {
                foreach (array_keys(self::LEGACY) as $column) {
                    if (Schema::hasColumn('setting', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (! Schema::hasColumn('setting', 'mail_api_config')) {
            try {
                DB::statement('ALTER TABLE `setting` ROW_FORMAT=DYNAMIC');
            } catch (\Throwable $e) {
            }

            Schema::table('setting', function (Blueprint $table) {
                $after = Schema::hasColumn('setting', 'mail_http_client_secret')
                    ? 'mail_http_client_secret'
                    : (Schema::hasColumn('setting', 'exchange_auth_method') ? 'exchange_auth_method' : null);
                $col = $table->mediumText('mail_api_config')->nullable();
                if ($after) {
                    $col->after($after);
                }
            });
        }

        foreach ($payloads as $id => $json) {
            DB::table('setting')->where('id', $id)->update(['mail_api_config' => $json]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('setting') || ! Schema::hasColumn('setting', 'mail_api_config')) {
            return;
        }

        $rows = DB::table('setting')->select(['id', 'mail_api_config'])->get();

        Schema::table('setting', function (Blueprint $table) {
            foreach (array_keys(self::LEGACY) as $column) {
                if (! Schema::hasColumn('setting', $column)) {
                    $table->text($column)->nullable();
                }
            }
        });

        foreach ($rows as $row) {
            $cfg = json_decode((string) ($row->mail_api_config ?? ''), true) ?: [];
            $update = [];
            foreach (self::LEGACY as $column => $jsonKey) {
                $update[$column] = $cfg[$jsonKey] ?? null;
            }
            DB::table('setting')->where('id', $row->id)->update($update);
        }
    }
};
