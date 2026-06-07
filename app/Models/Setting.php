<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $table="setting";
    protected $hidden =["default_password"];
    public $timestamps =false;

    protected $casts = [
        'moodle_sync_enabled' => 'boolean',
        'frappe_sync_enabled' => 'boolean',
        'openedx_sync_enabled' => 'boolean',
    ];

    public function getLogoAttribute($photo){
        return storage_link('uploads/config/'.$photo);
    }

}
