<?php

namespace App\Services;

/**
 * Enhanced Exchange OAuth Handler Service
 * 
 * Handles multiple OAuth 2.0 flows with Microsoft Graph API:
 * - Authorization Code Flow (user-based)
 * - Client Credentials Flow (application-based)
 * - Automatic token refresh
 * - Background refresh support
 * - Comprehensive error handling
 * 
 * @author SendMail ExchangeEmailService
 * @version 2.0.0
 */
class ExchangeEmailService
{
    protected $tenantId;
    protected $clientId;
    protected $clientSecret;
    protected $redirectUri;
    protected $scope;
    protected $accessToken;
    protected $refreshToken;
    protected $tokenExpiresAt;
    protected $authMethod;
    protected $fromEmail;
    protected $fromName;
    protected $tokenFile;

    // Supported authentication methods
    const AUTH_AUTHORIZATION_CODE = 'authorization_code';
    const AUTH_CLIENT_CREDENTIALS = 'client_credentials';

    public function __construct($tenantId = null, $clientId = null, $clientSecret = null, $redirectUri = null, $scope = null, $authMethod = null)
    {
        $this->tenantId = $tenantId ?: env('EXCHANGE_TENANT_ID');
        $this->clientId = $clientId ?: env('EXCHANGE_CLIENT_ID');
        $this->clientSecret = $clientSecret ?: env('EXCHANGE_CLIENT_SECRET');
        $this->redirectUri = $redirectUri ?: env('EXCHANGE_REDIRECT_URI');
        $this->scope = $scope ?: env('EXCHANGE_SCOPE') ?: 'https://graph.microsoft.com/Mail.Send';
        $this->authMethod = $authMethod ?: env('EXCHANGE_AUTH_METHOD') ?: self::AUTH_CLIENT_CREDENTIALS;
        $this->fromEmail = env('MAIL_FROM_ADDRESS');
        $this->fromName = env('MAIL_FROM_NAME', 'Africa CDC Knowledge Hub');
        
        // Set token file path
        $this->tokenFile = storage_path('app/tokens/oauth_tokens.json');
        
        $this->loadStoredTokens();
    }

    /**
     * Check if OAuth is configured
     */
    public function isConfigured()
    {
        return !empty($this->tenantId) && 
               !empty($this->clientId) && 
               !empty($this->clientSecret);
    }

    /**
     * Check if we have valid tokens
     */
    public function hasValidToken()
    {
        return !empty($this->accessToken) && 
               $this->tokenExpiresAt && 
               time() < $this->tokenExpiresAt;
    }

    /**
     * Get authorization URL (for Authorization Code Flow)
     */
    public function getAuthorizationUrl()
    {
        if (!$this->isConfigured()) {
            throw new \Exception('OAuth not configured');
        }

        if ($this->authMethod !== self::AUTH_AUTHORIZATION_CODE) {
            throw new \Exception('Authorization URL only available for Authorization Code Flow');
        }

        $state = bin2hex(random_bytes(16));
        session(['oauth_state' => $state]);

        $params = [
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'redirect_uri' => $this->redirectUri,
            'scope' => $this->scope,
            'response_mode' => 'query',
            'state' => $state
        ];

        return 'https://login.microsoftonline.com/' . $this->tenantId . '/oauth2/v2.0/authorize?' . http_build_query($params);
    }

    /**
     * Exchange authorization code for tokens (Authorization Code Flow)
     */
    public function exchangeCodeForToken($code, $state)
    {
        if (!$this->isConfigured()) {
            throw new \Exception('OAuth not configured');
        }

        // Verify state parameter
        if (session('oauth_state') !== $state) {
            throw new \Exception('Invalid state parameter');
        }

        $tokenUrl = 'https://login.microsoftonline.com/' . $this->tenantId . '/oauth2/v2.0/token';
        
        $data = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'redirect_uri' => $this->redirectUri,
            'grant_type' => 'authorization_code',
            'scope' => $this->scope
        ];

        $response = $this->makeHttpRequest($tokenUrl, 'POST', $data);
        
        if (isset($response['access_token'])) {
            $this->accessToken = $response['access_token'];
            $this->refreshToken = $response['refresh_token'] ?? null;
            $this->tokenExpiresAt = time() + ($response['expires_in'] ?? 3600);
            
            $this->storeTokens();
            session()->forget('oauth_state');
            
            return true;
        }

        throw new \Exception('Failed to exchange code for token: ' . ($response['error_description'] ?? 'Unknown error'));
    }

    /**
     * Get access token using Client Credentials Flow
     */
    public function getClientCredentialsToken()
    {
        if (!$this->isConfigured()) {
            throw new \Exception('OAuth not configured');
        }

        $tokenUrl = 'https://login.microsoftonline.com/' . $this->tenantId . '/oauth2/v2.0/token';
        
        $data = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'scope' => 'https://graph.microsoft.com/.default',
            'grant_type' => 'client_credentials'
        ];

        $response = $this->makeHttpRequest($tokenUrl, 'POST', $data);
        
        if (isset($response['access_token'])) {
            $this->accessToken = $response['access_token'];
            $this->refreshToken = null; // Client credentials don't have refresh tokens
            $this->tokenExpiresAt = time() + ($response['expires_in'] ?? 3600);
            $this->authMethod = self::AUTH_CLIENT_CREDENTIALS;
            
            $this->storeTokens();
            return true;
        }

        throw new \Exception('Failed to get client credentials token: ' . ($response['error_description'] ?? 'Unknown error'));
    }

    /**
     * Refresh access token
     */
    public function refreshAccessToken()
    {
        // For client credentials, get a new token
        if ($this->authMethod === self::AUTH_CLIENT_CREDENTIALS) {
            return $this->getClientCredentialsToken();
        }

        // For authorization code flow, use refresh token
        if (!$this->refreshToken) {
            return false;
        }

        $tokenUrl = 'https://login.microsoftonline.com/' . $this->tenantId . '/oauth2/v2.0/token';
        
        $data = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'refresh_token' => $this->refreshToken,
            'grant_type' => 'refresh_token',
            'scope' => $this->scope
        ];

        $response = $this->makeHttpRequest($tokenUrl, 'POST', $data);
        
        if (isset($response['access_token'])) {
            $this->accessToken = $response['access_token'];
            $this->refreshToken = $response['refresh_token'] ?? $this->refreshToken;
            $this->tokenExpiresAt = time() + ($response['expires_in'] ?? 3600);
            
            $this->storeTokens();
            return true;
        }

        return false;
    }

    /**
     * Get access token with automatic refresh
     */
    public function getAccessToken()
    {
        // Check if token needs refresh (5 minutes buffer)
        if ($this->accessToken && $this->tokenExpiresAt && time() < ($this->tokenExpiresAt - 300)) {
            return $this->accessToken;
        }

        // Try to refresh token
        if ($this->refreshAccessToken()) {
            return $this->accessToken;
        }

        // If client credentials, try to get new token
        if ($this->authMethod === self::AUTH_CLIENT_CREDENTIALS) {
            if ($this->getClientCredentialsToken()) {
                return $this->accessToken;
            }
        }

        throw new \Exception('Unable to obtain valid access token');
    }

    /**
     * Send email via Microsoft Graph API
     */
    public function sendEmail($to, $subject, $body, $isHtml = true, $fromEmail = null, $fromName = null, $cc = [], $bcc = [], $attachments = [])
    {
        // Get valid access token
        $accessToken = $this->getAccessToken();

        $fromEmail = $fromEmail ?: $this->fromEmail;
        $fromName = $fromName ?: $this->fromName;

        // For client credentials, we need to specify the user
        $sendUrl = 'https://graph.microsoft.com/v1.0/me/sendMail';
        if ($this->authMethod === self::AUTH_CLIENT_CREDENTIALS) {
            $sendUrl = 'https://graph.microsoft.com/v1.0/users/' . urlencode($fromEmail) . '/sendMail';
        }

        $emailData = [
            'message' => [
                'subject' => $subject,
                'body' => [
                    'contentType' => $isHtml ? 'HTML' : 'Text',
                    'content' => $body
                ],
                'toRecipients' => array_map(function($email) {
                    return ['emailAddress' => ['address' => $email]];
                }, is_array($to) ? $to : [$to]),
                'from' => [
                    'emailAddress' => [
                        'address' => $fromEmail,
                        'name' => $fromName
                    ]
                ]
            ]
        ];

        // Add CC recipients if provided
        if (!empty($cc)) {
            $emailData['message']['ccRecipients'] = array_map(function($email) {
                return ['emailAddress' => ['address' => $email]];
            }, $cc);
        }

        // Add BCC recipients if provided
        if (!empty($bcc)) {
            $emailData['message']['bccRecipients'] = array_map(function($email) {
                return ['emailAddress' => ['address' => $email]];
            }, $bcc);
        }

        // Add attachments if provided
        if (!empty($attachments)) {
            $emailData['message']['attachments'] = array_map(function($attachment) {
                return [
                    '@odata.type' => '#microsoft.graph.fileAttachment',
                    'name' => $attachment['name'],
                    'contentType' => $attachment['content_type'] ?? 'application/octet-stream',
                    'contentBytes' => base64_encode($attachment['content'])
                ];
            }, $attachments);
        }

        $response = $this->makeHttpRequest($sendUrl, 'POST', $emailData, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ]);

        return !isset($response['error']);
    }

    /**
     * Test email sending with current configuration
     */
    public function testEmailSending($testEmail = 'test@example.com')
    {
        try {
            $subject = 'Exchange Email Service Test - ' . date('Y-m-d H:i:s');
            $body = $this->getTestEmailBody($testEmail);
            
            return $this->sendEmail($testEmail, $subject, $body);
        } catch (\Exception $e) {
            \Log::error('Email test failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get test email body
     */
    protected function getTestEmailBody($testEmail)
    {
        return '
        <html>
        <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
            <div style="background: linear-gradient(135deg, #119A48 0%, #0e7a3a 100%); color: white; padding: 20px; text-align: center;">
                <h1>✅ Exchange Email Service Test</h1>
                <p>Africa CDC Knowledge Hub - Microsoft Graph API</p>
            </div>
            
            <div style="padding: 20px;">
                <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <h3>🎉 Email Service Working Perfectly!</h3>
                    <p>This email confirms that your Exchange Email Service is working correctly.</p>
                </div>
                
                <h3>Configuration Details:</h3>
                <ul>
                    <li><strong>Method:</strong> Microsoft Graph API</li>
                    <li><strong>Authentication:</strong> ' . ucfirst(str_replace('_', ' ', $this->authMethod)) . '</li>
                    <li><strong>Security:</strong> Bearer Token Authentication</li>
                    <li><strong>Sent At:</strong> ' . date('Y-m-d H:i:s T') . '</li>
                    <li><strong>Recipient:</strong> ' . htmlspecialchars($testEmail) . '</li>
                    <li><strong>From:</strong> ' . htmlspecialchars($this->fromEmail) . '</li>
                    <li><strong>Service:</strong> Africa CDC Knowledge Hub</li>
                </ul>
                
                <div style="background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <h4>🚀 Enhanced Features:</h4>
                    <ul>
                        <li>✅ Multiple OAuth Flows</li>
                        <li>✅ Automatic Token Refresh</li>
                        <li>✅ Client Credentials Support</li>
                        <li>✅ Background Refresh</li>
                        <li>✅ Production Ready</li>
                        <li>✅ Comprehensive Error Handling</li>
                    </ul>
                </div>
                
                <p><strong>Your Exchange Email Service is ready for production! 🎉</strong></p>
            </div>
            
            <div style="background: #f8f9fa; padding: 15px; text-align: center; font-size: 12px; color: #6c757d;">
                <p>This is an automated test email from Africa CDC Knowledge Hub</p>
                <p>Generated on ' . date('Y-m-d H:i:s') . ' | Microsoft Graph API</p>
            </div>
        </body>
        </html>';
    }

    /**
     * Load stored tokens from file
     */
    protected function loadStoredTokens()
    {
        try {
            if (file_exists($this->tokenFile)) {
                $tokenData = json_decode(file_get_contents($this->tokenFile), true);
                
                if ($tokenData && isset($tokenData[$this->clientId])) {
                    $tokens = $tokenData[$this->clientId];
                    $this->accessToken = $tokens['access_token'] ?? null;
                    $this->refreshToken = $tokens['refresh_token'] ?? null;
                    $this->tokenExpiresAt = $tokens['expires_at'] ?? null;
                    $this->authMethod = $tokens['auth_method'] ?? $this->authMethod;
                }
            }
        } catch (\Exception $e) {
            // Ignore file errors, continue without stored tokens
        }
    }

    /**
     * Store tokens in file
     */
    protected function storeTokens()
    {
        try {
            // Create tokens directory if it doesn't exist
            $tokenDir = dirname($this->tokenFile);
            if (!is_dir($tokenDir)) {
                mkdir($tokenDir, 0755, true);
            }
            
            // Load existing tokens
            $tokenData = [];
            if (file_exists($this->tokenFile)) {
                $tokenData = json_decode(file_get_contents($this->tokenFile), true) ?: [];
            }
            
            // Update tokens for this client
            $tokenData[$this->clientId] = [
                'access_token' => $this->accessToken,
                'refresh_token' => $this->refreshToken,
                'expires_at' => $this->tokenExpiresAt,
                'auth_method' => $this->authMethod,
                'updated_at' => time()
            ];
            
            // Save to file
            file_put_contents($this->tokenFile, json_encode($tokenData, JSON_PRETTY_PRINT));
            
        } catch (\Exception $e) {
            // Ignore file errors
        }
    }


    /**
     * Make HTTP request with enhanced error handling
     */
    protected function makeHttpRequest($url, $method = 'GET', $data = null, $headers = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                if (is_array($data)) {
                    // For OAuth token requests, use form-encoded data
                    if (strpos($url, '/oauth2/v2.0/token') !== false) {
                        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
                    } else {
                        // For other requests, use JSON
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                        $headers[] = 'Content-Type: application/json';
                    }
                } else {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                }
            }
        }

        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new \Exception('cURL error: ' . $error);
        }

        $decodedResponse = json_decode($response, true);
        
        if ($httpCode >= 400) {
            $errorMessage = 'HTTP error ' . $httpCode;
            if ($decodedResponse) {
                $errorMessage .= ': ' . ($decodedResponse['error_description'] ?? $decodedResponse['error']['message'] ?? json_encode($decodedResponse));
            } else {
                $errorMessage .= ': ' . $response;
            }
            throw new \Exception($errorMessage);
        }

        return $decodedResponse ?: $response;
    }

    /**
     * Get refresh token
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * Get token expiration time
     */
    public function getTokenExpiresAt()
    {
        return $this->tokenExpiresAt;
    }

    /**
     * Get authentication method
     */
    public function getAuthMethod()
    {
        return $this->authMethod;
    }

    /**
     * Clear stored tokens
     */
    public function clearTokens()
    {
        $this->accessToken = null;
        $this->refreshToken = null;
        $this->tokenExpiresAt = null;
        
        try {
            if (file_exists($this->tokenFile)) {
                $tokenData = json_decode(file_get_contents($this->tokenFile), true) ?: [];
                unset($tokenData[$this->clientId]);
                file_put_contents($this->tokenFile, json_encode($tokenData, JSON_PRETTY_PRINT));
            }
        } catch (\Exception $e) {
            // Ignore file errors
        }
    }
}

