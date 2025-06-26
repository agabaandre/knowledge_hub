<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiData extends Model
{
    use HasFactory;
    protected $table ="kpi_data_view";
    public $timestamps = false;
    public function kpi(){

        return $this->belongsTo(Kpi::class);
    }
}
