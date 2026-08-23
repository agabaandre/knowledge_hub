<?php

use App\Models\Faq;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('faqs')) {
            return;
        }

        $items = [
            [
                'question' => 'Where is the user guide?',
                'answer' => 'Open User Guide in the site footer, or go to /user_manual. Reviewers and administrators also have an administrator guide at /administrator-guide. These pages stay in sync with the docs/ folder in the project.',
            ],
            [
                'question' => 'How do reviewers approve publications, forums, or community requests?',
                'answer' => 'Open Admin → Approvals (/admin/approvals). Filter by publications, forums, community participants, or federated partner content. Approve or reject items from that inbox. Email notifications and the daily 08:00 summary link to the same page. Approved and rejected records remain on their original admin lists.',
            ],
            [
                'question' => 'Why did a publication or theme URL change after it was renamed?',
                'answer' => 'Public URLs use slugs generated from the current title or name. When that name is saved with a new value, the slug is regenerated. Search for the resource if an old bookmark 404s. Administrators can rebuild slugs with php artisan slugs:regenerate (use --only-empty to fill blanks without changing existing URLs).',
            ],
            [
                'question' => 'How do I browse content from partner country hubs?',
                'answer' => 'On a continental hub, open /federated. Linked partner hubs appear in a carousel above the listings (flag background; click to filter). Publications and forums appear after they are synced and approved. Country hubs connect under Admin → Federated Knowledge Hubs.',
            ],
            [
                'question' => 'Do administrative-unit pages use the same publication cards as search?',
                'answer' => 'Yes. On country hubs, /adminunits/details?id=… shows that unit’s publications with the same cards as records search: cover, excerpt, preview, download, favourite, and Khub AI where enabled.',
            ],
        ];

        foreach ($items as $item) {
            $exists = Faq::query()->where('question', $item['question'])->exists();
            if ($exists) {
                Faq::query()->where('question', $item['question'])->update([
                    'answer' => $item['answer'],
                    'updated_at' => now(),
                ]);
            } else {
                Faq::query()->create($item);
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('faqs')) {
            return;
        }

        Faq::query()->whereIn('question', [
            'Where is the user guide?',
            'How do reviewers approve publications, forums, or community requests?',
            'Why did a publication or theme URL change after it was renamed?',
            'How do I browse content from partner country hubs?',
            'Do administrative-unit pages use the same publication cards as search?',
        ])->delete();
    }
};
