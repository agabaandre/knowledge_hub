<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'rating')) {
                // Add rating column after summary if it exists, otherwise add at the end
                if (Schema::hasColumn('courses', 'summary')) {
                    $table->decimal('rating', 3, 2)->default(0.00)->after('summary')->comment('Course rating from 0.00 to 5.00');
                } else {
                    $table->decimal('rating', 3, 2)->default(0.00)->comment('Course rating from 0.00 to 5.00');
                }
            }
        });

        // Assign random default ratings to existing courses (between 3.0 and 5.0)
        $courses = DB::table('courses')->get();
        foreach ($courses as $course) {
            // Generate random rating between 3.0 and 5.0, rounded to 2 decimal places
            $randomRating = round(rand(300, 500) / 100, 2);
            DB::table('courses')->where('id', $course->id)->update(['rating' => $randomRating]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'rating')) {
                $table->dropColumn('rating');
            }
        });
    }
};
