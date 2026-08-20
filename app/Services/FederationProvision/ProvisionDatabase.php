<?php

namespace App\Services\FederationProvision;

use Illuminate\Support\Str;
use PDO;
use RuntimeException;

class ProvisionDatabase
{
    /**
     * @return array{database: string, username: string, password: string, host: string, port: int}
     */
    public function createForSlug(string $slug): array
    {
        $database = ProvisionSlug::databaseName($slug);
        $username = ProvisionSlug::databaseUsername($slug);
        $password = Str::password(24);

        $pdo = $this->adminPdo();

        $pdo->exec('CREATE DATABASE IF NOT EXISTS `'.$this->quoteIdent($database).'` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

        $host = '%';
        $pdo->exec(
            "CREATE USER IF NOT EXISTS '".$this->quoteIdent($username)."'@'".$host."' IDENTIFIED BY ".$pdo->quote($password)
        );
        // MySQL < 5.7 / MariaDB variants without IF NOT EXISTS for users:
        try {
            $pdo->exec(
                "CREATE USER '".$this->quoteIdent($username)."'@'localhost' IDENTIFIED BY ".$pdo->quote($password)
            );
        } catch (\Throwable) {
            // User may already exist from a previous attempt.
        }

        $pdo->exec(
            'GRANT ALL PRIVILEGES ON `'.$this->quoteIdent($database).'`.* TO \''.$this->quoteIdent($username).'\'@\'%\''
        );
        try {
            $pdo->exec(
                'GRANT ALL PRIVILEGES ON `'.$this->quoteIdent($database).'`.* TO \''.$this->quoteIdent($username).'\'@\'localhost\''
            );
        } catch (\Throwable) {
            // ignore
        }
        $pdo->exec('FLUSH PRIVILEGES');

        // Ensure password is set even if user already existed.
        try {
            $pdo->exec(
                "ALTER USER '".$this->quoteIdent($username)."'@'%' IDENTIFIED BY ".$pdo->quote($password)
            );
        } catch (\Throwable) {
            // ignore
        }
        try {
            $pdo->exec(
                "ALTER USER '".$this->quoteIdent($username)."'@'localhost' IDENTIFIED BY ".$pdo->quote($password)
            );
        } catch (\Throwable) {
            // ignore
        }

        return [
            'database' => $database,
            'username' => $username,
            'password' => $password,
            'host' => (string) config('federation_provision.mysql.host'),
            'port' => (int) config('federation_provision.mysql.port'),
        ];
    }

    public function dropForSlug(string $slug, ?string $database = null, ?string $username = null): void
    {
        $database = $database ?: ProvisionSlug::databaseName($slug);
        $username = $username ?: ProvisionSlug::databaseUsername($slug);
        $pdo = $this->adminPdo();

        try {
            $pdo->exec('DROP DATABASE IF EXISTS `'.$this->quoteIdent($database).'`');
        } catch (\Throwable) {
            // best effort
        }

        foreach (['%', 'localhost'] as $host) {
            try {
                $pdo->exec("DROP USER IF EXISTS '".$this->quoteIdent($username)."'@'".$host."'");
            } catch (\Throwable) {
                try {
                    $pdo->exec("DROP USER '".$this->quoteIdent($username)."'@'".$host."'");
                } catch (\Throwable) {
                    // best effort
                }
            }
        }

        try {
            $pdo->exec('FLUSH PRIVILEGES');
        } catch (\Throwable) {
            // ignore
        }
    }

    protected function adminPdo(): PDO
    {
        $cfg = config('federation_provision.mysql');
        $user = (string) ($cfg['admin_user'] ?? '');
        $pass = (string) ($cfg['admin_password'] ?? '');
        if ($user === '') {
            throw new RuntimeException('FEDERATION_PROVISION_MYSQL_ADMIN_USER is not configured.');
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%d;charset=utf8mb4',
            $cfg['host'] ?? '127.0.0.1',
            (int) ($cfg['port'] ?? 3306)
        );

        try {
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
        } catch (\Throwable $e) {
            throw new RuntimeException('Could not connect with MySQL admin credentials.');
        }

        return $pdo;
    }

    protected function quoteIdent(string $value): string
    {
        if (! preg_match('/^[A-Za-z0-9_]+$/', $value)) {
            throw new RuntimeException('Invalid SQL identifier.');
        }

        return $value;
    }
}
