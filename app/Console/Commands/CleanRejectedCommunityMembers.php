<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CommunityOfPracticeMembers;

class CleanRejectedCommunityMembers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commsofpractice:clean-rejected';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete rejected community membership requests (is_approved = 2). Runs daily at midnight.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $count = CommunityOfPracticeMembers::where('is_approved', 2)->count();
        CommunityOfPracticeMembers::where('is_approved', 2)->delete();

        $this->info("Cleaned {$count} rejected community membership request(s).");

        return Command::SUCCESS;
    }
}
