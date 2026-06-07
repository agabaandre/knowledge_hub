<?php

namespace App\Contracts;

interface LearningProviderSync
{
    public function providerKey(): string;

    public function providerLabel(): string;

    public function isConfigured(): bool;

    public function syncEnabled(): bool;

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    public function testConnection(array $overrides = []): array;

    /**
     * @param  null|callable(int $current, int $total, string $label): void  $onProgress
     */
    public function fetchAndStoreCourses(?callable $onProgress = null): int;
}
