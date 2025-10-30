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

        // Create a new Request object
        $request = new Request();

        // Populate the request with user data from Microsoft
        $request->merge([
            'firstname' => $firstName,
            'lastname' => $lastName,
            'email' => $email,
            'country_id' => null,
            'phone' => null,
            'job' => (is_array($userData) ? ($userData['jobTitle'] ?? null) : ($userData->jobTitle ?? null)),
            'subscribe' => null,
            'photo' => $socialUser->getAvatar() ?? (is_array($userData) ? ($userData['picture'] ?? null) : ($userData->picture ?? null)),
            'preferences' => null,
            'social_provider' => 'microsoft'
        ]);

        if($request->photo):
            $request->is_photo_external = 1;
        endif;

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
        $request->merge([
            'firstname' => $user->user->given_name, // Extracting first name
            'lastname' => $user->user->family_name, // Extracting last name
            'email' => $user->user->email, // Extracting email
            'country_id' => null, // Set this if you have a way to determine the country
            'phone' => null, // Set this if you have a way to determine the phone
            'job' => null, // Google does not provide job title by default
            'photo' => $user->user->picture ?? null, // Extracting profile picture if available
            'preferences' => null, // Handle user preferences if needed
            'social_provider'=>'google'
        ]);

        if($request->photo):
            $request->is_photo_external = 1;
        endif;

        // Call the save method in UsersRepository
        $savedUser = $this->usersRepo->save($request, true); // Pass true for social login

        return $savedUser;
    }
}