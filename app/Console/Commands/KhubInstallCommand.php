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
        {--files-root= : Host files root (default /var/khubdata/files)}
        {--sql-backup-root= : SQL backup root (default /var/khubdata/backups/sql)}
        {--storage-driver=internal : Files driver (internal, s3, gcs, azure, sharepoint, sftp)}
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
                'host' => config('database.connections.mysql.host', '127.0.0.1'),
                'port' => config('database.connections.mysql.port', '3306'),
                'database' => config('database.connections.mysql.database', 'knowledge_hub'),
                'username' => config('database.connections.mysql.username', 'root'),
                'password' => config('database.connections.mysql.password', ''),
            ]);
        } else {
            $this->info('Skipping migrations (existing database)...');
            $installer->configureDatabaseConnection([
                'host' => config('database.connections.mysql.host', '127.0.0.1'),
                'port' => config('database.connections.mysql.port', '3306'),
                'database' => config('database.connections.mysql.database', 'knowledge_hub'),
                'username' => config('database.connections.mysql.username', 'root'),
                'password' => config('database.connections.mysql.password', ''),
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
            'mail_from_address' => config('mail.from.address', $email),
            'mail_from_name' => config('mail.from.name', config('app.name', 'Knowledge Hub')),
            'mail_host' => config('mail.mailers.smtp.host', ''),
            'mail_port' => config('mail.mailers.smtp.port', '587'),
            'mail_username' => config('mail.mailers.smtp.username', ''),
            'mail_password' => config('mail.mailers.smtp.password', ''),
            'mail_encryption' => config('mail.mailers.smtp.encryption', 'tls') ?: 'none',
        ]);

        $storageDefaults = $installer->storageDefaults();
        $installer->configureStorage([
            'files_driver' => (string) $this->option('storage-driver'),
            'local_files_root' => (string) ($this->option('files-root') ?: $storageDefaults['files_root']),
            'sql_backup_root' => (string) ($this->option('sql-backup-root') ?: $storageDefaults['sql_backup_root']),
            'auto_sql_backup' => true,
        ]);

        $siteName = (string) ($this->option('site-name') ?: config('app.name', 'Knowledge Hub'));
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

        $installer->finalize(config('app.url', 'http://localhost'));

        $this->info('Installation complete.');

        return self::SUCCESS;
    }
}
