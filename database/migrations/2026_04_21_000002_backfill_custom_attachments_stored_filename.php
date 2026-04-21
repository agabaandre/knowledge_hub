<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BackfillCustomAttachmentsStoredFilename extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('custom_attachments', 'stored_filename')) {
            return;
        }

        DB::table('custom_attachments')
            ->whereNull('stored_filename')
            ->orderBy('id')
            ->chunkById(500, function ($rows) {
                foreach ($rows as $row) {
                    $path = (string) ($row->path ?? '');
                    if ($path === '' || preg_match('#^https?://#i', $path)) {
                        continue;
                    }
                    $base = basename(str_replace('\\', '/', $path));
                    if ($base !== '' && $base !== '.' && $base !== '..') {
                        DB::table('custom_attachments')->where('id', $row->id)->update(['stored_filename' => $base]);
                    }
                }
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // no-op
    }
}
