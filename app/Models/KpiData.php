<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiData extends Model
{
    use HasFactory;
    protected $table ="data";

    public function kpi(){

        return $this->belongsTo(Kpi::class);
    }
}
