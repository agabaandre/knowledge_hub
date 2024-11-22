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

    public function microsoftCallback($user) {
    
        Log::info('Microsoft Response', (array) $user);

        // Create a new Request object
        $request = new Request();

        // Populate the request with user data from Microsoft
        $request->merge([
            'firstname' => $user->user->givenName, // Extracting first name
            'lastname' => $user->user->surname, // Extracting last name
            'email' => $user->user->mail ?? '', // Use mail if available, otherwise fallback to getEmail()
            'country_id' => null, // Set this if you have a way to determine the country
            'phone' => null, // Set this if you have a way to determine the phone
            'job' => $user->user->jobTitle ?? null, // Extracting job title if available
            'subscribe' => null, // Set this if you have a subscription option
            'photo' => $user->user->picture ?? null, // Handle photo upload if needed
            'preferences' => null,// Handle user preferences if needed
            'social_provider'=>'microsoft'
        ]);

        if($request->photo):
            $request->is_photo_external = 1;
        endif;

        // Call the save method in UsersRepository
        $savedUser = $this->usersRepo->save($request, true); // Pass true for social login

        return $savedUser;
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