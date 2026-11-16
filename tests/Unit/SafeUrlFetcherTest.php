<?php

namespace Tests\Unit;

use App\Support\SafeUrlFetcher;
use Tests\TestCase;

class SafeUrlFetcherTest extends TestCase
{
    public function test_loopback_and_private_urls_are_rejected(): void
    {
        foreach ([
            'http://127.0.0.1/',
            'http://127.0.0.1:80/',
            'http://127.8.4.4/x',
            'https://10.0.0.5/secret',
            'http://172.16.3.2/',
            'http://192.168.1.1/router',
            'http://169.254.169.254/latest/meta-data/',
            'http://100.64.1.1/',
            'http://[::1]/',
            'http://[fe80::1]/',
            'http://[fd00::2]/',
            'http://[::ffff:127.0.0.1]/',
        ] as $url) {
            $this->assertNull(SafeUrlFetcher::validate($url), "Should reject {$url}");
        }
    }

    public function test_non_http_schemes_and_credentials_are_rejected(): void
    {
        $this->assertNull(SafeUrlFetcher::validate('ftp://example.org/file'));
        $this->assertNull(SafeUrlFetcher::validate('file:///etc/passwd'));
        $this->assertNull(SafeUrlFetcher::validate('gopher://example.org/'));
        $this->assertNull(SafeUrlFetcher::validate('javascript:alert(1)'));
        $this->assertNull(SafeUrlFetcher::validate('http://user:pass@93.184.216.34/'));
        $this->assertNull(SafeUrlFetcher::validate(''));
        $this->assertNull(SafeUrlFetcher::validate(null));
    }

    public function test_public_ip_urls_are_accepted(): void
    {
        $this->assertSame('https://93.184.216.34/paper.pdf', SafeUrlFetcher::validate('https://93.184.216.34/paper.pdf'));
        $this->assertSame('http://8.8.8.8/', SafeUrlFetcher::validate('http://8.8.8.8/'));
    }

    public function test_forbidden_ip_matrix(): void
    {
        $this->assertTrue(SafeUrlFetcher::isForbiddenIp('127.0.0.1'));
        $this->assertTrue(SafeUrlFetcher::isForbiddenIp('10.1.2.3'));
        $this->assertTrue(SafeUrlFetcher::isForbiddenIp('172.31.255.255'));
        $this->assertTrue(SafeUrlFetcher::isForbiddenIp('192.168.0.1'));
        $this->assertTrue(SafeUrlFetcher::isForbiddenIp('169.254.169.254'));
        $this->assertTrue(SafeUrlFetcher::isForbiddenIp('100.100.1.1'));
        $this->assertTrue(SafeUrlFetcher::isForbiddenIp('0.0.0.0'));
        $this->assertTrue(SafeUrlFetcher::isForbiddenIp('::1'));
        $this->assertTrue(SafeUrlFetcher::isForbiddenIp('fc00::1'));
        $this->assertTrue(SafeUrlFetcher::isForbiddenIp('fe80::1'));
        $this->assertFalse(SafeUrlFetcher::isForbiddenIp('93.184.216.34'));
        $this->assertFalse(SafeUrlFetcher::isForbiddenIp('2606:2800:220:1:248:1893:25c8:1946'));
    }

    public function test_publication_link_rule_uses_the_same_policy(): void
    {
        $rule = new \App\Rules\SafePublicUrl();
        $this->assertFalse($rule->passes('link', 'http://127.0.0.1/'));
        $this->assertFalse($rule->passes('link', 'http://169.254.169.254/'));
        $this->assertTrue($rule->passes('link', 'https://93.184.216.34/doc'));
        $this->assertTrue($rule->passes('link', null));
    }
}
