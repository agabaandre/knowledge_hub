<?php 
namespace App\Services;

use App\Repositories\UsersRepository;
use App\Support\OAuthAccountSecurity;
use Illuminate\Http\Request;
use Log;

class SocialLoginService {

    private $usersRepo;

    public function __construct(UsersRepository $usersRepository) {
        $this->usersRepo = $usersRepository;
    }

    public function microsoftCallback($socialUser) {
    
        try {
            Log::info('Microsoft Response', [
                'email' => method_exists($socialUser, 'getEmail') ? $socialUser->getEmail() : null,
                'name' => method_exists($socialUser, 'getName') ? $socialUser->getName() : null,
                'id' => method_exists($socialUser, 'getId') ? $socialUser->getId() : null,
            ]);

        // Get user data from Microsoft - try both array access and object access
        $userData = [];
        try {
            if (property_exists($socialUser, 'user')) {
                $userData = $socialUser->user ?? [];
            } elseif (method_exists($socialUser, 'getRaw')) {
                $rawUser = $socialUser->getRaw();
                $userData = is_array($rawUser) ? $rawUser : (is_object($rawUser) ? (array)$rawUser : []);
            }
        } catch (\Exception $e) {
            Log::warning('Could not extract user data from Microsoft response', ['error' => $e->getMessage()]);
        }
        
        // Extract name parts - Microsoft provides displayName, we need to split it
        $name = method_exists($socialUser, 'getName') ? (string) ($socialUser->getName() ?? '') : '';
        $nameParts = explode(' ', $name, 2);
        $firstName = $nameParts[0] ?? '';
        $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

        // Try to get givenName and surname from user data if available
        if (is_array($userData)) {
            $firstName = $userData['givenName'] ?? $userData['given_name'] ?? $firstName;
            $lastName = $userData['surname'] ?? $userData['family_name'] ?? $lastName;
        } elseif (is_object($userData)) {
            $firstName = $userData->givenName ?? $userData->given_name ?? $firstName;
            $lastName = $userData->surname ?? $userData->family_name ?? $lastName;
        }

        // Get email - Microsoft may use 'mail' or 'email' or we use getEmail()
        $email = method_exists($socialUser, 'getEmail') ? $socialUser->getEmail() : null;
        if (!$email && is_array($userData)) {
            $email = $userData['mail'] ?? $userData['email'] ?? $userData['userPrincipalName'] ?? '';
        } elseif (!$email && is_object($userData)) {
            $email = $userData->mail ?? $userData->email ?? $userData->userPrincipalName ?? '';
        }

        $email = OAuthAccountSecurity::normalizedProviderEmail($email);
        if (! $email) {
            Log::error('Microsoft Callback: No valid email found', ['userData' => $userData]);
            return null;
        }

        // Get user photo from Microsoft - try multiple methods
        $photoUrl = null;
        try {
            // Try getAvatar() method first (Socialite standard)
            if (method_exists($socialUser, 'getAvatar')) {
                $photoUrl = $socialUser->getAvatar();
            }
            
            // If not available, try to get from raw data
            if (!$photoUrl && method_exists($socialUser, 'getRaw')) {
                $rawUser = $socialUser->getRaw();
                if ($rawUser) {
                    if (is_array($rawUser)) {
                        $photoUrl = $rawUser['photo'] ?? $rawUser['picture'] ?? null;
                    } elseif (is_object($rawUser)) {
                        $photoUrl = $rawUser->photo ?? $rawUser->picture ?? null;
                    }
                }
            }
            
            // Also try from userData
            if (!$photoUrl && !empty($userData)) {
                if (is_array($userData)) {
                    $photoUrl = $userData['photo'] ?? $userData['picture'] ?? null;
                } elseif (is_object($userData)) {
                    $photoUrl = $userData->photo ?? $userData->picture ?? null;
                }
            }
            
            // Try accessing Microsoft Graph photo endpoint format
            if (!$photoUrl && method_exists($socialUser, 'getId')) {
                $userId = $socialUser->getId();
                if ($userId) {
                    // Microsoft Graph API photo endpoint format
                    $photoUrl = "https://graph.microsoft.com/v1.0/me/photo/\$value";
                }
            }
            
            Log::info('Microsoft Photo Retrieved', [
                'email' => $email,
                'photoUrl' => $photoUrl ? substr($photoUrl, 0, 100) : 'no'
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to get Microsoft photo', ['error' => $e->getMessage()]);
        }

        $photoUrl = OAuthAccountSecurity::sanitizeStoredAvatarUrl($photoUrl);

        // Create a new Request object
        $request = new Request();

        // Populate the request with user data from Microsoft
        $requestData = [
            'firstname' => $firstName,
            'lastname' => $lastName,
            'email' => $email,
            'phone' => null,
            'job' => (is_array($userData) ? ($userData['jobTitle'] ?? null) : ($userData->jobTitle ?? null)),
            'subscribe' => null,
            'photo' => $photoUrl,
            'preferences' => null,
            'social_provider' => 'microsoft'
        ];

        if($photoUrl):
            $requestData['is_photo_external'] = 1;
        endif;

        $request->merge($requestData);

        // Call the save method in UsersRepository
        $savedUser = $this->usersRepo->save($request, true); // Pass true for social login

        if ($savedUser) {
            // Auto-assign to Africa CDC Staff (ID: 31) only for africacdc.org emails
            try {
                $africaCDCCommunityId = 31;
                $emailLower = strtolower($savedUser->email ?? '');
                $isAfricaCDC = (bool) preg_match('/@africacdc\.org$/i', $emailLower);
                if(!$isAfricaCDC){
                    return $savedUser;
                }
                
                // Check if user is already a member
                $existingMember = \App\Models\CommunityOfPracticeMembers::where('user_id', $savedUser->id)
                    ->where('community_of_practice_id', $africaCDCCommunityId)
                    ->first();
                
                if (!$existingMember) {
                    \App\Models\CommunityOfPracticeMembers::create([
                        'user_id' => $savedUser->id,
                        'community_of_practice_id' => $africaCDCCommunityId,
                        'is_approved' => 1, // Auto-approve staff members
                    ]);
                    
                    Log::info('Auto-assigned user to Africa CDC Staff community', [
                        'user_id' => $savedUser->id,
                        'email' => $savedUser->email,
                        'community_id' => $africaCDCCommunityId
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to auto-assign user to community', [
                    'user_id' => $savedUser->id,
                    'error' => $e->getMessage()
                ]);
                // Don't fail the login if community assignment fails
            }
        }

        return $savedUser;
            
        } catch (\Exception $e) {
            Log::error('Microsoft Callback Exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return null;
        }
    }

    /**
     * @param  \Laravel\Socialite\Contracts\User|\Laravel\Socialite\Two\User|object  $socialUser  Web Socialite user or API-shaped object with ->user
     */
    public function googleCallback($socialUser)
    {
        $u = [];
        if (isset($socialUser->user)) {
            $u = is_array($socialUser->user) ? $socialUser->user : (array) $socialUser->user;
        }

        $rawEmail = method_exists($socialUser, 'getEmail')
            ? $socialUser->getEmail()
            : ($u['email'] ?? '');
        $email = OAuthAccountSecurity::normalizedProviderEmail($rawEmail);
        if (! $email) {
            Log::error('Google Callback: No valid email');

            return null;
        }

        $firstname = $u['given_name'] ?? $u['givenName'] ?? '';
        $lastname = $u['family_name'] ?? $u['surname'] ?? '';
        if ($firstname === '' && $lastname === '' && method_exists($socialUser, 'getName')) {
            $parts = preg_split('/\s+/', trim((string) $socialUser->getName()), 2, PREG_SPLIT_NO_EMPTY);
            $firstname = $parts[0] ?? '';
            $lastname = $parts[1] ?? '';
        }

        $photoUrl = OAuthAccountSecurity::sanitizeStoredAvatarUrl($u['picture'] ?? null);

        $request = new Request();
        $requestData = [
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'phone' => null,
            'job' => null,
            'photo' => $photoUrl,
            'preferences' => null,
            'social_provider' => 'google',
        ];

        if ($photoUrl) {
            $requestData['is_photo_external'] = 1;
        }

        $request->merge($requestData);

        return $this->usersRepo->save($request, true);
    }

    public function linkedinCallback($socialUser) {
    
        try {
            Log::info('LinkedIn Response', [
                'email' => method_exists($socialUser, 'getEmail') ? $socialUser->getEmail() : null,
                'name' => method_exists($socialUser, 'getName') ? $socialUser->getName() : null,
                'id' => method_exists($socialUser, 'getId') ? $socialUser->getId() : null,
            ]);

            // Get user data from LinkedIn - try both array access and object access
            $userData = [];
            try {
                if (property_exists($socialUser, 'user')) {
                    $userData = $socialUser->user ?? [];
                } elseif (method_exists($socialUser, 'getRaw')) {
                    $rawUser = $socialUser->getRaw();
                    $userData = is_array($rawUser) ? $rawUser : (is_object($rawUser) ? (array)$rawUser : []);
                }
            } catch (\Exception $e) {
                Log::warning('Could not extract user data from LinkedIn response', ['error' => $e->getMessage()]);
            }
            
            // Extract name parts - LinkedIn provides formatted name, we need to split it
            $name = method_exists($socialUser, 'getName') ? (string) ($socialUser->getName() ?? '') : '';
            $nameParts = explode(' ', $name, 2);
            $firstName = $nameParts[0] ?? '';
            $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

            // Try to get firstName and lastName from user data if available
            if (is_array($userData)) {
                $firstName = $userData['firstName'] ?? $userData['first_name'] ?? $userData['localizedFirstName'] ?? $firstName;
                $lastName = $userData['lastName'] ?? $userData['last_name'] ?? $userData['localizedLastName'] ?? $lastName;
            } elseif (is_object($userData)) {
                $firstName = $userData->firstName ?? $userData->first_name ?? $userData->localizedFirstName ?? $firstName;
                $lastName = $userData->lastName ?? $userData->last_name ?? $userData->localizedLastName ?? $lastName;
            }

            // Get email - LinkedIn requires email permission
            $email = method_exists($socialUser, 'getEmail') ? $socialUser->getEmail() : null;
            if (!$email && is_array($userData)) {
                $email = $userData['email'] ?? $userData['emailAddress'] ?? '';
            } elseif (!$email && is_object($userData)) {
                $email = $userData->email ?? $userData->emailAddress ?? '';
            }

            $email = OAuthAccountSecurity::normalizedProviderEmail($email);
            if (! $email) {
                Log::error('LinkedIn Callback: No valid email found', ['userData' => $userData]);
                return null;
            }

            // Get user photo from LinkedIn
            $photoUrl = null;
            try {
                // Try getAvatar() method first (Socialite standard)
                if (method_exists($socialUser, 'getAvatar')) {
                    $photoUrl = $socialUser->getAvatar();
                }
                
                // If not available, try to get from raw data
                if (!$photoUrl && method_exists($socialUser, 'getRaw')) {
                    $rawUser = $socialUser->getRaw();
                    if ($rawUser) {
                        if (is_array($rawUser)) {
                            $photoUrl = $rawUser['profilePicture'] ?? $rawUser['profile_picture'] ?? $rawUser['picture'] ?? null;
                            // LinkedIn v2 API provides profilePicture->displayImage
                            if (!$photoUrl && isset($rawUser['profilePicture']['displayImage'])) {
                                $photoUrl = $rawUser['profilePicture']['displayImage'];
                            }
                        } elseif (is_object($rawUser)) {
                            $photoUrl = $rawUser->profilePicture ?? $rawUser->profile_picture ?? $rawUser->picture ?? null;
                            if (!$photoUrl && isset($rawUser->profilePicture->displayImage)) {
                                $photoUrl = $rawUser->profilePicture->displayImage;
                            }
                        }
                    }
                }
                
                // Also try from userData
                if (!$photoUrl && !empty($userData)) {
                    if (is_array($userData)) {
                        $photoUrl = $userData['profilePicture'] ?? $userData['profile_picture'] ?? $userData['picture'] ?? null;
                    } elseif (is_object($userData)) {
                        $photoUrl = $userData->profilePicture ?? $userData->profile_picture ?? $userData->picture ?? null;
                    }
                }
                
                Log::info('LinkedIn Photo Retrieved', [
                    'email' => $email,
                    'photoUrl' => $photoUrl ? substr($photoUrl, 0, 100) : 'no'
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to get LinkedIn photo', ['error' => $e->getMessage()]);
            }

            $photoUrl = OAuthAccountSecurity::sanitizeStoredAvatarUrl($photoUrl);

            // Get job title from LinkedIn if available
            $jobTitle = null;
            if (is_array($userData)) {
                $jobTitle = $userData['headline'] ?? $userData['positions'] ?? null;
            } elseif (is_object($userData)) {
                $jobTitle = $userData->headline ?? $userData->positions ?? null;
            }

            // Create a new Request object
            $request = new Request();

            // Populate the request with user data from LinkedIn
            $requestData = [
                'firstname' => $firstName,
                'lastname' => $lastName,
                'email' => $email,
                'phone' => null,
                'job' => $jobTitle,
                'subscribe' => null,
                'photo' => $photoUrl,
                'preferences' => null,
                'social_provider' => 'linkedin'
            ];

            if($photoUrl):
                $requestData['is_photo_external'] = 1;
            endif;

            $request->merge($requestData);

            // Call the save method in UsersRepository
            $savedUser = $this->usersRepo->save($request, true); // Pass true for social login

            return $savedUser;
            
        } catch (\Exception $e) {
            Log::error('LinkedIn Callback Exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return null;
        }
    }
}