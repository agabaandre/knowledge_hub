<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Publication;
use App\Models\PublicationView;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate existing visits data to monthly views table
        // Distribute existing visits across months (rough estimate)
        $publications = Publication::where('visits', '>', 0)->get();
        
        foreach ($publications as $pub) {
            $totalVisits = $pub->visits ?? 0;
            if ($totalVisits > 0) {
                // Get publication creation date
                $createdAt = $pub->created_at ?? now();
                $createdYear = $createdAt->year;
                $createdMonth = $createdAt->month;
                
                // Calculate months since creation
                $now = now();
                $monthsSinceCreation = ($now->year - $createdYear) * 12 + ($now->month - $createdMonth) + 1;
                $monthsSinceCreation = max(1, $monthsSinceCreation); // At least 1 month
                
                // Distribute visits evenly across months (or put all in current month if recent)
                if ($monthsSinceCreation <= 3) {
                    // If less than 3 months old, put all views in current month
                    PublicationView::updateOrCreate(
                        [
                            'publication_id' => $pub->id,
                            'year' => $now->year,
                            'month' => $now->month,
                        ],
                        [
                            'views' => $totalVisits,
                        ]
                    );
                } else {
                    // Distribute evenly across last 6 months
                    $viewsPerMonth = max(1, floor($totalVisits / 6));
                    $remainingViews = $totalVisits % 6;
                    
                    for ($i = 0; $i < 6; $i++) {
                        $date = $now->copy()->subMonths(5 - $i);
                        $views = $viewsPerMonth + ($i < $remainingViews ? 1 : 0);
                        
                        PublicationView::updateOrCreate(
                            [
                                'publication_id' => $pub->id,
                                'year' => $date->year,
                                'month' => $date->month,
                            ],
                            [
                                'views' => $views,
                            ]
                        );
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally clear monthly views data
        // PublicationView::truncate();
    }
};

