<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;

class BotProtection
{
    /**
     * List of legitimate search engine user agents
     * These bots are allowed through
     */
    protected $allowedBots = [
        'googlebot',
        'bingbot',
        'slurp', // Yahoo
        'duckduckbot',
        'baiduspider',
        'yandexbot',
        'sogou',
        'exabot',
        'facebot',
        'ia_archiver',
        'google-structured-data-testing-tool',
        'google page speed',
        'pingdom',
        'facebookexternalhit',
        'twitterbot',
        'rogerbot',
        'linkedinbot',
        'embedly',
        'quora link preview',
        'showyoubot',
        'outbrain',
        'pinterest',
        'developers.google.com/+/web/snippet',
        'applebot',
        'flipboard',
        'tumblr',
        'bitlybot',
        'skypeuripreview',
        'nuzzel',
        'discordbot',
        'qwantify',
        'pinterestbot',
        'bitrix link preview',
        'xing-contenttabreceiver',
        'chrome-lighthouse',
        'google web preview',
        'semrushbot',
        'ahrefsbot',
        'dotbot',
        'mj12bot',
        'megaindex',
        'blexbot',
        'petalbot',
    ];

    /**
     * List of known malicious/bad bot user agents
     */
    protected $blockedBots = [
        'scrapy',
        'python-requests',
        'curl',
        'wget',
        'httpie',
        'libwww-perl',
        'lwp-trivial',
        'php',
        'masscan',
        'masscan',
        'nmap',
        'nikto',
        'sqlmap',
        'zap',
        'burp',
        'arachni',
        'acunetix',
        'netsparker',
        'grab',
        'go-http-client',
        'java/',
        'apache-httpclient',
        'okhttp',
        'scraper',
        'crawler',
        'bot',
        'spider',
        'harvest',
        'extract',
        'copy',
        'fetch',
        'wget',
        'download',
        'getright',
        'teleport',
        'webzip',
        'webcopier',
        'teleport',
        'blackwidow',
        'netmechanic',
        'netspider',
        'net vampire',
        'telesoft',
        'website extractor',
        'website qq',
        'webstripper',
        'webzip',
        'wget',
        'www collector',
        'httrack',
        'microsoft url control',
        'xenu',
        'zeus',
        'semrush',
        'ahrefs',
        'mj12bot',
        'dotbot',
        'blexbot',
        'petalbot',
        'megaindex',
        'masscan',
        'nmap',
        'nikto',
        'sqlmap',
        'zap',
        'burp',
        'arachni',
        'acunetix',
        'netsparker',
        'grab',
        'go-http-client',
        'java/',
        'apache-httpclient',
        'okhttp',
        'scraper',
        'crawler',
        'bot',
        'spider',
        'harvest',
        'extract',
        'copy',
        'fetch',
        'wget',
        'download',
        'getright',
        'teleport',
        'webzip',
        'webcopier',
        'teleport',
        'blackwidow',
        'netmechanic',
        'netspider',
        'net vampire',
        'telesoft',
        'website extractor',
        'website qq',
        'webstripper',
        'webzip',
        'wget',
        'www collector',
        'httrack',
        'microsoft url control',
        'xenu',
        'zeus',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // API routes use their own throttling/auth; heuristic bot checks break mobile apps
        // and OAuth flows (e.g. JSON POST to /api/social-login with a browser-like UA).
        if ($request->is('api') || $request->is('api/*')) {
            return $next($request);
        }

        $userAgent = strtolower($request->header('User-Agent', ''));
        
        // If no user agent, it's suspicious
        if (empty($userAgent)) {
            return $this->blockRequest($request, 'No User-Agent');
        }

        // Check if it's a legitimate search engine bot
        $isAllowedBot = $this->isAllowedBot($userAgent);
        
        // Check if it's a known malicious bot
        $isBlockedBot = $this->isBlockedBot($userAgent);
        
        // If it's a blocked bot, deny access
        if ($isBlockedBot && !$isAllowedBot) {
            return $this->blockRequest($request, 'Blocked Bot: ' . $userAgent);
        }

        // Check for suspicious patterns
        if (!$isAllowedBot && $this->isSuspicious($request, $userAgent)) {
            return $this->blockRequest($request, 'Suspicious Activity');
        }

        // Rate limiting for non-SEO bots
        if (!$isAllowedBot) {
            $key = 'bot_protection:' . $request->ip();
            
            if (RateLimiter::tooManyAttempts($key, 100)) { // 100 requests per minute
                Log::warning('Bot protection rate limit exceeded', [
                    'ip' => $request->ip(),
                    'user_agent' => $userAgent,
                    'url' => $request->fullUrl()
                ]);
                
                return response()->json([
                    'error' => 'Too many requests. Please slow down.'
                ], 429)->header('Retry-After', '60');
            }
            
            RateLimiter::hit($key, 60); // 60 seconds
        }

        return $next($request);
    }

    /**
     * Check if user agent is an allowed search engine bot
     */
    protected function isAllowedBot($userAgent)
    {
        foreach ($this->allowedBots as $allowedBot) {
            if (strpos($userAgent, $allowedBot) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user agent is a known malicious bot
     */
    protected function isBlockedBot($userAgent)
    {
        foreach ($this->blockedBots as $blockedBot) {
            if (strpos($userAgent, $blockedBot) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check for suspicious patterns
     */
    protected function isSuspicious(Request $request, $userAgent)
    {
        // Check for multiple suspicious indicators
        $suspiciousCount = 0;

        // 1. No referer on direct access (normal browsers usually have one)
        if (!$request->header('Referer') && !$request->header('Accept-Language')) {
            $suspiciousCount++;
        }

        // 2. Missing common browser headers
        if (!$request->header('Accept') || !$request->header('Accept-Language')) {
            $suspiciousCount++;
        }

        // 3. Too many requests from same IP
        $key = 'suspicious:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 200)) { // 200 requests per minute
            $suspiciousCount += 2;
        }
        RateLimiter::hit($key, 60);

        // 4. User agent contains suspicious keywords but not in allowed list
        $suspiciousKeywords = ['bot', 'crawler', 'spider', 'scraper'];
        $hasSuspiciousKeyword = false;
        foreach ($suspiciousKeywords as $keyword) {
            if (strpos($userAgent, $keyword) !== false) {
                $hasSuspiciousKeyword = true;
                break;
            }
        }
        
        if ($hasSuspiciousKeyword && !$this->isAllowedBot($userAgent)) {
            $suspiciousCount++;
        }

        // 5. Very short or very long user agent
        if (strlen($userAgent) < 10 || strlen($userAgent) > 500) {
            $suspiciousCount++;
        }

        // If multiple suspicious indicators, block
        return $suspiciousCount >= 3;
    }

    /**
     * Block the request
     */
    protected function blockRequest(Request $request, $reason)
    {
        Log::warning('Bot protection blocked request', [
            'ip' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'url' => $request->fullUrl(),
            'reason' => $reason
        ]);

        // Return a 403 Forbidden response
        return response()->view('errors.403', [
            'message' => 'Access denied',
            'resolvedErrorMessage' => 'Access denied',
            'exception' => null,
        ], 403);
    }
}

