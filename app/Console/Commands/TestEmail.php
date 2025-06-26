<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendMailJob;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $req = ["email"=>"agabaandre@gmail.com","subject"=>"Test","body"=>"Hello"];
        SendMailJob::dispatch($req);
        return true;
    }
}
