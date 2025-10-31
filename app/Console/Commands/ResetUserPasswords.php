<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetUserPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:reset-passwords {--password=password : The password to set for all users} {--force : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset all user passwords to a specified value (default: password)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $newPassword = $this->option('password');
        
        $this->info('Starting password reset process...');
        
        // Get all users
        $users = User::all();
        $totalUsers = $users->count();
        
        if ($totalUsers === 0) {
            $this->warn('No users found in the database.');
            return 0;
        }
        
        $this->info("Found {$totalUsers} user(s).");
        
        if (!$this->option('force') && !$this->confirm("Are you sure you want to reset passwords for all {$totalUsers} user(s) to '{$newPassword}'?")) {
            $this->info('Operation cancelled.');
            return 0;
        }
        
        $hashedPassword = Hash::make($newPassword);
        $bar = $this->output->createProgressBar($totalUsers);
        $bar->start();
        
        $updated = 0;
        foreach ($users as $user) {
            $user->password = $hashedPassword;
            $user->save();
            $updated++;
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        
        $this->info("Successfully reset passwords for {$updated} user(s) to '{$newPassword}'.");
        
        return 0;
    }
}
