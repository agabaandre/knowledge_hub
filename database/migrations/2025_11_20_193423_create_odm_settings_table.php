<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateOdmSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('odm_settings');
        Schema::create('odm_settings', function (Blueprint $table) {
            $table->integer('id')->unsigned()->autoIncrement();
            $table->string('name', 255);
            $table->string('value', 255);
            $table->string('description', 255);
            $table->string('validation', 255);
            
            $table->unique('name');
        });
        
        // Insert initial data
        $settings = [
            ['name' => 'debug', 'value' => 'False', 'description' => '(True/False) - Default=False - Debug the installation (not working)', 'validation' => 'bool'],
            ['name' => 'demo', 'value' => 'False', 'description' => '(True/False) This setting is for a demo installation, where random people will be all loggging in as the same username/password like "demo/demo". This will keep users from removing files, users, etc.', 'validation' => 'bool'],
            ['name' => 'authen', 'value' => 'mysql', 'description' => '(Default = mysql) Currently only MySQL authentication is supported', 'validation' => ''],
            ['name' => 'title', 'value' => 'Document Repository', 'description' => 'This is the browser window title', 'validation' => 'maxsize=255'],
            ['name' => 'site_mail', 'value' => 'root@localhost', 'description' => 'The email address of the administrator of this site', 'validation' => 'email|maxsize=255|req'],
            ['name' => 'root_id', 'value' => '1', 'description' => 'This variable sets the root user id.  The root user will be able to access all files and have authority for everything.', 'validation' => 'num|req'],
            ['name' => 'dataDir', 'value' => '/var/www/document_repository/', 'description' => 'location of file repository. This should ideally be outside the Web server root. Make sure the server has permissions to read/write files to this folder!. (Examples: Linux - /var/www/document_repository/ : Windows - c:/document_repository/', 'validation' => 'maxsize=255'],
            ['name' => 'max_filesize', 'value' => '5000000', 'description' => 'Set the maximum file upload size', 'validation' => 'num|maxsize=255'],
            ['name' => 'revision_expiration', 'value' => '90', 'description' => 'This var sets the amount of days until each file needs to be revised,  assuming that there are 30 days in a month for all months.', 'validation' => 'num|maxsize=255'],
            ['name' => 'file_expired_action', 'value' => '1', 'description' => 'Choose an action option when a file is found to be expired The first two options also result in sending email to reviewer  (1) Remove from file list until renewed (2) Show in file list but non-checkoutable (3) Send email to reviewer only (4) Do Nothing', 'validation' => 'num'],
            ['name' => 'authorization', 'value' => 'True', 'description' => 'True or False. If set True, every document must be reviewed by an admin before it can go public. To disable set to False. If False, all newly added/checked-in documents will immediately be listed', 'validation' => 'bool'],
            ['name' => 'allow_signup', 'value' => 'False', 'description' => 'Should we display the sign-up link?', 'validation' => 'bool'],
            ['name' => 'allow_password_reset', 'value' => 'False', 'description' => 'Should we allow users to reset their forgotten password?', 'validation' => 'bool'],
            ['name' => 'try_nis', 'value' => 'False', 'description' => 'Attempt NIS password lookups from YP server?', 'validation' => 'bool'],
            ['name' => 'theme', 'value' => 'tweeter', 'description' => 'Which theme to use?', 'validation' => ''],
            ['name' => 'language', 'value' => 'english', 'description' => 'Set the default language (english, spanish, turkish, etc.). Local users may override this setting. Check include/language folder for languages available', 'validation' => 'alpha|req'],
            ['name' => 'max_query', 'value' => '500', 'description' => 'Set this to the maximum number of rows you want to be returned in a file listing.', 'validation' => 'num'],
        ];
        
        foreach ($settings as $setting) {
            DB::table('odm_settings')->insert($setting);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('odm_settings');
    }
}

