<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateOdmFiletypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('odm_filetypes');
        Schema::create('odm_filetypes', function (Blueprint $table) {
            $table->integer('id')->unsigned()->autoIncrement();
            $table->string('type', 255);
            $table->tinyInteger('active');
        });
        
        // Insert initial data
        $filetypes = [
            ['type' => 'image/gif', 'active' => 1],
            ['type' => 'text/html', 'active' => 1],
            ['type' => 'text/plain', 'active' => 1],
            ['type' => 'application/pdf', 'active' => 1],
            ['type' => 'image/pdf', 'active' => 1],
            ['type' => 'application/x-pdf', 'active' => 1],
            ['type' => 'application/msword', 'active' => 1],
            ['type' => 'image/jpeg', 'active' => 1],
            ['type' => 'image/pjpeg', 'active' => 1],
            ['type' => 'image/png', 'active' => 1],
            ['type' => 'application/msexcel', 'active' => 1],
            ['type' => 'application/msaccess', 'active' => 1],
            ['type' => 'text/richtxt', 'active' => 1],
            ['type' => 'application/mspowerpoint', 'active' => 1],
            ['type' => 'application/octet-stream', 'active' => 1],
            ['type' => 'application/x-zip-compressed', 'active' => 1],
            ['type' => 'application/x-zip', 'active' => 1],
            ['type' => 'application/zip', 'active' => 1],
            ['type' => 'image/tiff', 'active' => 1],
            ['type' => 'image/tif', 'active' => 1],
            ['type' => 'application/vnd.ms-powerpoint', 'active' => 1],
            ['type' => 'application/vnd.ms-excel', 'active' => 1],
            ['type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'active' => 1],
            ['type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'active' => 1],
            ['type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'active' => 1],
            ['type' => 'application/vnd.oasis.opendocument.chart', 'active' => 1],
            ['type' => 'application/vnd.oasis.opendocument.chart-template', 'active' => 1],
            ['type' => 'application/vnd.oasis.opendocument.formula', 'active' => 1],
            ['type' => 'application/vnd.oasis.opendocument.formula-template', 'active' => 1],
            ['type' => 'application/vnd.oasis.opendocument.graphics', 'active' => 1],
            ['type' => 'application/vnd.oasis.opendocument.graphics-template', 'active' => 1],
            ['type' => 'application/vnd.oasis.opendocument.image', 'active' => 1],
            ['type' => 'application/vnd.oasis.opendocument.image-template', 'active' => 1],
            ['type' => 'application/vnd.oasis.opendocument.presentation', 'active' => 1],
            ['type' => 'application/vnd.oasis.opendocument.presentation-template', 'active' => 1],
            ['type' => 'application/vnd.oasis.opendocument.spreadsheet', 'active' => 1],
            ['type' => 'application/vnd.oasis.opendocument.spreadsheet-template', 'active' => 1],
            ['type' => 'application/vnd.oasis.opendocument.text', 'active' => 1],
            ['type' => 'application/vnd.oasis.opendocument.text-master', 'active' => 1],
            ['type' => 'application/vnd.oasis.opendocument.text-template', 'active' => 1],
            ['type' => 'application/vnd.oasis.opendocument.text-web', 'active' => 1],
            ['type' => 'text/csv', 'active' => 1],
            ['type' => 'audio/mpeg', 'active' => 0],
            ['type' => 'image/x-dwg', 'active' => 1],
            ['type' => 'image/x-dfx', 'active' => 1],
            ['type' => 'drawing/x-dwf', 'active' => 1],
            ['type' => 'image/svg', 'active' => 1],
            ['type' => 'video/3gpp', 'active' => 1],
        ];
        
        foreach ($filetypes as $filetype) {
            DB::table('odm_filetypes')->insert($filetype);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('odm_filetypes');
    }
}

