<?php

namespace App\Services\FederationProvision;

use RuntimeException;
use Symfony\Component\Process\Process;

class SudoRunner
{
    public function run(array $command, ?string $cwd = null, int $timeout = 600): string
    {
        $password = (string) config('federation_provision.sudo_password', '');
        if ($password === '') {
            throw new RuntimeException('FEDERATION_PROVISION_SUDO_PASSWORD is not configured.');
        }

        $wrapped = array_merge(['sudo', '-S', '-p', ''], $command);
        $process = new Process($wrapped, $cwd, null, $password."\n", $timeout);
        $process->run();

        if (! $process->isSuccessful()) {
            $error = trim($process->getErrorOutput() ?: $process->getOutput());
            $error = $this->redact($error, $password);
            throw new RuntimeException('Privileged command failed: '.$error);
        }

        return $process->getOutput();
    }

    public function runShell(string $shell, ?string $cwd = null, int $timeout = 600): string
    {
        return $this->run(['/bin/bash', '-lc', $shell], $cwd, $timeout);
    }

    protected function redact(string $text, string $password): string
    {
        if ($password === '') {
            return $text;
        }

        return str_replace($password, '***', $text);
    }
}
