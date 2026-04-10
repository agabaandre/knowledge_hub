<?php

namespace App\Jobs;

use App\Models\AccessLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AccessLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    private $ip_address;

    /** @var array|\Illuminate\Http\Request */
    private $request;

    /** @var int|string|null Authenticated user id when the hit was logged (queue workers have no session). */
    private $userId;

    /**
     * Create a new job instance.
     *
     * @param  array|\Illuminate\Http\Request  $request
     */
    public function __construct($ip, $request, $userId = null)
    {
        $this->ip_address = $ip;
        $this->request = $request;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            // Skip logging for private/local IPs or known bots
            if ($this->isPrivateIp($this->ip_address) || $this->isBot()) {
                return;
            }

            $geoData = null;
            $apiUrl = "http://ipinfo.io/{$this->ip_address}/json";
            $context = stream_context_create([
                'http' => [
                    'timeout' => 2.5,
                    'ignore_errors' => true,
                ]
            ]);
            try {
                $response = @file_get_contents($apiUrl, false, $context);
                if ($response) {
                    $geoData  = json_decode($response);
                }
            } catch (\Throwable $e) {
                // Non-fatal, fall back to minimal data
                Log::debug('Geo lookup failed', ['ip' => $this->ip_address, 'error' => $e->getMessage()]);
            }

            $country = strtoupper(@$geoData->country ?: '');
            $city    = @$geoData->city ?: '';
            $loc     = @$geoData->loc ?: '';
            $lat     = '';
            $long    = '';
            if (!empty($loc) && strpos($loc, ',') !== false) {
                [$lat, $long] = explode(',', $loc, 2);
            }

            // Infer resource info from request
            $publicationId = null;
            try {
                if (is_array($this->request)) {
                    $publicationId = $this->request['id'] ?? null;
                } elseif ($this->request instanceof \Illuminate\Http\Request) {
                    $publicationId = $this->request->input('id');
                    if (!$publicationId) {
                        $path = $this->request->path();
                        if (stripos($path, 'records/resource') !== false) {
                            $publicationId = $this->request->query('id');
                        }
                    }
                }
            } catch (\Throwable $e) {
                // ignore
            }

            $locationLog = new AccessLog();
            $locationLog->ip_address = $this->ip_address;
            $locationLog->country    = $country ?: 'UNKNOWN';
            $locationLog->city       = $city ?: null;
            $locationLog->lat        = $lat ?: null;
            $locationLog->long       = $long ?: null;
            $locationLog->publication_id = $publicationId;
            $uid = $this->userId ?? optional(current_user())->id;
            $locationLog->user_id = $uid !== null && $uid !== '' ? (string) $uid : null;

            // Best-effort save without crashing
            try { $locationLog->save(); } catch (\Throwable $e) {
                Log::debug('AccessLog save failed', ['error' => $e->getMessage()]);
            }

        } catch(\Throwable $exception){
            Log::debug('AccessLogJob exception', ['error' => $exception->getMessage()]);
        }
    }

    private function isPrivateIp($ip): bool
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) return true;
        $long = ip2long($ip);
        // 10.0.0.0/8, 172.16.0.0/12, 192.168.0.0/16, 127.0.0.0/8
        $private = (
            ($long >= ip2long('10.0.0.0')    && $long <= ip2long('10.255.255.255')) ||
            ($long >= ip2long('172.16.0.0')  && $long <= ip2long('172.31.255.255')) ||
            ($long >= ip2long('192.168.0.0') && $long <= ip2long('192.168.255.255')) ||
            ($long >= ip2long('127.0.0.0')   && $long <= ip2long('127.255.255.255'))
        );
        return $private;
    }

    private function isBot(): bool
    {
        try {
            $ua = '';
            if ($this->request instanceof \Illuminate\Http\Request) {
                $ua = (string) $this->request->header('User-Agent');
            } elseif (is_array($this->request)) {
                $ua = (string) ($this->request['HTTP_USER_AGENT'] ?? '');
            }
            $ua = strtolower($ua);
            if ($ua === '') return false;
            $bots = ['bot', 'spider', 'crawl', 'slurp', 'bingpreview', 'facebookexternalhit', 'curl'];
            foreach ($bots as $b) { if (strpos($ua, $b) !== false) return true; }
            return false;
        } catch (\Throwable $e) { return false; }
    }

}

