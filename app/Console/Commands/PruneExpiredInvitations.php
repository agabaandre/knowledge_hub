<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\CommsOfPracticeRepository;

class PruneExpiredInvitations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invitations:prune-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune expired community invitations that have not been responded to';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(CommsOfPracticeRepository $repository)
    {
        $this->info('Pruning expired invitations...');
        
        $deleted = $repository->pruneExpiredInvitations();
        
        $this->info("Deleted {$deleted} expired invitation(s).");
        
        return Command::SUCCESS;
    }
}
