<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PublicationView extends Model
{
    use HasFactory;

    protected $table = 'publication_views';
    
    protected $fillable = [
        'publication_id',
        'year',
        'month',
        'views',
    ];

    public $timestamps = true;

    /**
     * Get the publication that owns the view record.
     */
    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }

    /**
     * Increment views for a publication in the current month/year.
     */
    public static function incrementView($publicationId)
    {
        $now = now();
        $year = $now->year;
        $month = $now->month;

        $view = static::firstOrNew([
            'publication_id' => $publicationId,
            'year' => $year,
            'month' => $month,
        ]);

        $view->views = ($view->views ?? 0) + 1;
        $view->save();

        return $view;
    }

    /**
     * Get total views for a publication.
     */
    public static function getTotalViews($publicationId)
    {
        return static::where('publication_id', $publicationId)
            ->sum('views');
    }

    /**
     * Get views for a specific month/year.
     */
    public static function getViewsForMonth($publicationId, $year, $month)
    {
        return static::where('publication_id', $publicationId)
            ->where('year', $year)
            ->where('month', $month)
            ->value('views') ?? 0;
    }
}

