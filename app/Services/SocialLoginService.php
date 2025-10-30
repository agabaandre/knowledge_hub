<?php 
namespace App\Services;

use App\Repositories\UsersRepository;
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
                'email' => $socialUser->getEmail(),
                'name' => $socialUser->getName(),
                'id' => $socialUser->getId()
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
        $name = $socialUser->getName() ?? '';
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
        $email = $socialUser->getEmail();
        if (!$email && is_array($userData)) {
            $email = $userData['mail'] ?? $userData['email'] ?? $userData['userPrincipalName'] ?? '';
        } elseif (!$email && is_object($userData)) {
            $email = $userData->mail ?? $userData->email ?? $userData->userPrincipalName ?? '';
        }

        if (!$email) {
            Log::error('Microsoft Callback: No email found', ['userData' => $userData]);
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

        // Create a new Request object
        $request = new Request();

        // Populate the request with user data from Microsoft
        $requestData = [
            'firstname' => $firstName,
            'lastname' => $lastName,
            'email' => $email,
            'country_id' => null,
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

    public function googleCallback($user) {
       
        Log::info('Google Response', (array) $user);

        // Create a new Request object
        $request = new Request();

        // Populate the request with user data from Google
        $photoUrl = $user->user->picture ?? null;
        $requestData = [
            'firstname' => $user->user->given_name, // Extracting first name
            'lastname' => $user->user->family_name, // Extracting last name
            'email' => $user->user->email, // Extracting email
            'country_id' => null, // Set this if you have a way to determine the country
            'phone' => null, // Set this if you have a way to determine the phone
            'job' => null, // Google does not provide job title by default
            'photo' => $photoUrl, // Extracting profile picture if available
            'preferences' => null, // Handle user preferences if needed
            'social_provider'=>'google'
        ];

        if($photoUrl):
            $requestData['is_photo_external'] = 1;
        endif;

        $request->merge($requestData);

        // Call the save method in UsersRepository
        $savedUser = $this->usersRepo->save($request, true); // Pass true for social login

        return $savedUser;
    }
}