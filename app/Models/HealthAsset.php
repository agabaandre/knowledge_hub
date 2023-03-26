<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthAsset extends Model
{
    use HasFactory;

    protected $table="health_assets";

    public function type(){
        return $this->belongsTo(AssetType::class,"asset_type_id","id");
    }
    
}
