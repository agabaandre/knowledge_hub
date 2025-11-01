<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;

class CloseExpiredEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:close-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically close events that have passed their end date';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Closing expired events...');
        
        $closed = Event::where('status', '!=', 'cancelled')
            ->whereNotNull('enddate')
            ->where('enddate', '<', now())
            ->update(['status' => 'cancelled']);
        
        $this->info("Closed {$closed} expired event(s).");
        
        return Command::SUCCESS;
    }
}
