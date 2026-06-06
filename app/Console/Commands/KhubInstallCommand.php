<?php

namespace App\Console\Commands;

use App\Services\InstallerService;
use Illuminate\Console\Command;

class KhubInstallCommand extends Command
{
    protected $signature = 'khub:install
        {--email= : Admin email}
        {--password= : Admin password (min 8 chars)}
        {--first-name=Admin : Admin first name}
        {--last-name=User : Admin last name}
        {--mail-driver=log : Mail driver (log or smtp)}
        {--site-name= : Site name for setting row}
        {--skip-migrate : Skip migrations}';

    protected $description = 'Install Knowledge Hub: migrate database, seed baseline, create admin user';

    public function handle(InstallerService $installer): int
    {
        if ($installer->isInstalled()) {
            $this->error('Application is already installed.');

            return self::FAILURE;
        }

        if (! $this->option('skip-migrate')) {
            $this->info('Running migrations...');
            $installer->runDatabaseSetup([
                'host' => env('DB_HOST', '127.0.0.1'),
                'port' => env('DB_PORT', '3306'),
                'database' => env('DB_DATABASE', 'knowledge_hub'),
                'username' => env('DB_USERNAME', 'root'),
                'password' => env('DB_PASSWORD', ''),
            ]);
        } else {
            $this->info('Skipping migrations (existing database)...');
            $installer->configureDatabaseConnection([
                'host' => env('DB_HOST', '127.0.0.1'),
                'port' => env('DB_PORT', '3306'),
                'database' => env('DB_DATABASE', 'knowledge_hub'),
                'username' => env('DB_USERNAME', 'root'),
                'password' => env('DB_PASSWORD', ''),
            ]);
        }

        $email = $this->option('email') ?: $this->ask('Admin email');
        $password = $this->option('password') ?: $this->secret('Admin password (min 8 chars)');

        if (! filter_var($email, FILTER_VALIDATE_EMAIL) || strlen((string) $password) < 8) {
            $this->error('Valid email and password (8+ chars) are required.');

            return self::FAILURE;
        }

        $installer->writeMailConfig([
            'mail_mailer' => (string) $this->option('mail-driver'),
            'mail_from_address' => env('MAIL_FROM_ADDRESS', $email),
            'mail_from_name' => env('MAIL_FROM_NAME', env('APP_NAME', 'Knowledge Hub')),
            'mail_host' => env('MAIL_HOST', ''),
            'mail_port' => env('MAIL_PORT', '587'),
            'mail_username' => env('MAIL_USERNAME', ''),
            'mail_password' => env('MAIL_PASSWORD', ''),
            'mail_encryption' => env('MAIL_ENCRYPTION', 'tls') ?: 'none',
        ]);

        $siteName = (string) ($this->option('site-name') ?: env('APP_NAME', 'Knowledge Hub'));
        $installer->saveSiteSettings([
            'site_name' => $siteName,
            'site_description' => 'Welcome to '.$siteName,
            'contact_email' => $email,
            'timezone' => 'Africa/Nairobi',
        ]);

        $installer->createAdminUser([
            'email' => $email,
            'password' => $password,
            'first_name' => $this->option('first-name'),
            'last_name' => $this->option('last-name'),
        ]);

        $installer->finalize(env('APP_URL', 'http://localhost'));

        $this->info('Installation complete.');

        return self::SUCCESS;
    }
}
