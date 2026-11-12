<?php

namespace Tests\Unit;

use App\Services\InstallerService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class InstallerHardeningTest extends TestCase
{
    private string $lockPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->lockPath = storage_path('app/testing-installed.lock');
        config(['install.lock_file' => $this->lockPath]);
        config(['install.app_installed' => false]);
        config(['install.disabled' => false]);
        if (File::exists($this->lockPath)) {
            File::delete($this->lockPath);
        }
    }

    protected function tearDown(): void
    {
        if (File::exists($this->lockPath)) {
            File::delete($this->lockPath);
        }
        parent::tearDown();
    }

    public function test_app_installed_config_marks_installed(): void
    {
        config(['install.app_installed' => true]);

        $this->assertTrue(app(InstallerService::class)->isInstalled());
        $this->assertTrue(app(InstallerService::class)->hasNonDatabaseInstallSignal());
    }

    public function test_lock_file_marks_installed(): void
    {
        File::ensureDirectoryExists(dirname($this->lockPath));
        File::put($this->lockPath, now()->toIso8601String());

        $this->assertTrue(app(InstallerService::class)->isInstalled());
    }

    public function test_installer_disabled_marks_installed_and_flag(): void
    {
        config(['install.disabled' => true]);

        $installer = app(InstallerService::class);
        $this->assertTrue($installer->isInstallerDisabled());
        $this->assertTrue($installer->isInstalled());
    }

    public function test_database_outage_with_vendor_does_not_look_uninstalled(): void
    {
        config(['install.app_installed' => false]);
        config(['install.disabled' => false]);

        $installer = $this->getMockBuilder(InstallerService::class)
            ->onlyMethods(['isLockedInSettings', 'databaseHasExistingData', 'hasVendorPackages'])
            ->getMock();

        $installer->method('hasVendorPackages')->willReturn(true);
        $installer->method('isLockedInSettings')->willThrowException(new \RuntimeException('SQLSTATE connection refused'));

        $this->assertTrue($installer->isInstalled());
    }

    public function test_write_env_values_skips_when_env_not_writable(): void
    {
        $envPath = base_path('.env');
        if (! File::exists($envPath) || ! is_writable($envPath)) {
            $this->markTestSkipped('.env must exist and start writable to exercise the read-only skip path.');
        }

        $before = File::get($envPath);
        $originalPerms = fileperms($envPath) & 0777;

        Log::spy();
        chmod($envPath, 0444);
        $this->assertFalse(app(InstallerService::class)->isEnvFileWritable($envPath));

        try {
            app(InstallerService::class)->writeEnvValues(['APP_NAME' => 'ShouldNotWrite']);
            $this->assertSame($before, File::get($envPath));
            Log::shouldHaveReceived('warning')->withArgs(function ($message) {
                return is_string($message) && str_contains($message, 'not writable');
            })->atLeast()->once();
        } finally {
            chmod($envPath, $originalPerms ?: 0644);
        }
    }

    public function test_install_middleware_returns_404_when_installer_disabled(): void
    {
        config(['install.disabled' => true]);
        config(['install.app_installed' => true]);

        $middleware = new \App\Http\Middleware\RedirectIfInstalled();
        $request = \Illuminate\Http\Request::create('/install', 'GET');
        $request->setRouteResolver(function () {
            $route = new \Illuminate\Routing\Route(['GET'], 'install', fn () => null);
            $route->name('install.index');

            return $route;
        });

        try {
            $middleware->handle($request, fn () => response('should-not-run'));
            $this->fail('Expected HttpException 404');
        } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
            $this->assertSame(404, $e->getStatusCode());
        }
    }

    public function test_install_middleware_returns_404_when_already_installed_via_lock(): void
    {
        config(['install.disabled' => false]);
        config(['install.app_installed' => false]);
        File::ensureDirectoryExists(dirname($this->lockPath));
        File::put($this->lockPath, now()->toIso8601String());

        $middleware = new \App\Http\Middleware\RedirectIfInstalled();
        $request = \Illuminate\Http\Request::create('/install', 'GET');
        $request->setRouteResolver(function () {
            $route = new \Illuminate\Routing\Route(['GET'], 'install', fn () => null);
            $route->name('install.index');

            return $route;
        });

        try {
            $middleware->handle($request, fn () => response('should-not-run'));
            $this->fail('Expected HttpException 404');
        } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
            $this->assertSame(404, $e->getStatusCode());
        }
    }

    public function test_fix_storage_permissions_script_does_not_grant_world_write(): void
    {
        $script = File::get(base_path('fix-storage-permissions.sh'));
        $this->assertStringNotContainsString('o+rwx', $script);
        $this->assertStringContainsString('o-rwx', $script);
    }
}
