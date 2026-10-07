<?php

namespace Tests\Unit;

use App\Support\DockerServiceHost;
use Tests\TestCase;

class DockerServiceHostTest extends TestCase
{
    public function test_loopback_hosts_are_left_unchanged(): void
    {
        $this->assertSame('127.0.0.1', DockerServiceHost::resolve('127.0.0.1'));
        $this->assertSame('localhost', DockerServiceHost::resolve('localhost'));
        $this->assertSame('3306', DockerServiceHost::resolvePort('127.0.0.1', '3306', '3307'));
    }

    public function test_unresolvable_mysql_service_name_maps_to_localhost_published_port_outside_docker(): void
    {
        if (DockerServiceHost::insideContainer() || DockerServiceHost::hostnameResolves('mysql')) {
            $this->markTestSkipped('mysql hostname already resolves in this environment.');
        }

        $this->assertSame('127.0.0.1', DockerServiceHost::resolve('mysql'));
        $this->assertSame('3306', DockerServiceHost::resolvePort('mysql', '3306', '3306'));
        $this->assertSame('3307', DockerServiceHost::resolvePort('mysql', '3306', '3307'));
    }
}
