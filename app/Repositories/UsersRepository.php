<?php
namespace App\Repositories;

use App\Jobs\SendMailJob;
use App\Models\Author;
use App\Models\Country;
use App\Models\GeoCoverage;
use App\Models\JobTitle;
use App\Models\User;
use App\Models\UserPreference;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Mail;
use Laravel\Passport\Token;
use App\Models\CommunityOfPracticeMembers;

class UsersRepository {

    /**
     * Convert string to camel case (Title Case)
     */
    private function toCamelCase($str) {
        if (empty($str)) {
            return '';
        }
        // Convert to lowercase, then capitalize first letter of each word
        return Str::title(Str::lower(trim($str)));
    }

    public function save(Request $request,$is_social=false){

        $user = ($is_social)?User::where('email',$request->email)->first():(($request->id)?User::find($request->id):new User());

        if(!$user)
            $user =  new User();

        if (! $is_social && ! $user->id) {
            $user->is_social_login = false;
            $user->social_provider = null;
        }

        //don't update these values for social signups account edits
        if( (!$user->id || ($user->id && !$user->is_social_login))){

            $firstname = $request->firstname ?? $request->first_name;
            $lastname = $request->lastname ?? $request->last_name;
            
            if($firstname && $lastname) {
                $user->name          = $firstname." ".$lastname;
                $user->first_name    = $firstname;
                $user->last_name     = $lastname;
            }
            
            if($request->email)
                $user->email         = $request->email;

        }

        // Country is chosen on the account profile (or registration), never from OAuth—we do not merge country_id in social callbacks.
        if ($request->exists('country_id')) {
            $countryId = $request->input('country_id');
            $isEmpty = ($countryId === null || $countryId === '');
            if (! $isEmpty) {
                $user->country_id = $countryId;
            } elseif (! $is_social || ! $user->exists) {
                $user->country_id = null;
            }
        } elseif ($request->has('country')) {
            $countryId = $request->input('country');
            $user->country_id = $countryId !== '' ? $countryId : $user->country_id;
        }

        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'gender')) {
            $gender = $request->input('gender');
            $user->gender = in_array($gender, ['male', 'female'], true) ? $gender : null;
        }

        $user->phone_number  = ($request->phone ?? $request->phone_number)?($request->phone ?? $request->phone_number):$user->phone_number;
        
        // Handle job_title with camel case transformation
        // Backward compatibility: if job is posted as numeric ID, resolve to JobTitle name.
        $jobInput = $request->job_title_custom ?? $request->job_title ?? $request->job ?? null;
        if (is_string($jobInput) && ctype_digit(trim($jobInput))) {
            $resolvedName = JobTitle::query()->whereKey((int) $jobInput)->value('name');
            if ($resolvedName) {
                $request->merge(['job' => $resolvedName, 'job_title' => $resolvedName, 'job_title_custom' => $resolvedName]);
            }
        }

        if($request->job_title_custom) {
            $user->job_title = $this->toCamelCase($request->job_title_custom);
        } elseif($request->job_title) {
            $user->job_title = $this->toCamelCase($request->job_title);
        } elseif($request->job) {
            $user->job_title = $this->toCamelCase($request->job);
        } elseif($user->job) {
            $user->job_title = $this->toCamelCase($user->job);
        }
        
        // Handle organization_name with camel case transformation
        if($request->organization_name) {
            $user->organization_name = $this->toCamelCase($request->organization_name);
        }
        
        $user->orcid         = ($request->orcid)?$request->orcid:$user->orcid; 
        $user->is_photo_external = ($request->is_photo_external)?$request->is_photo_external:$user->is_photo_external;
        if($user->is_photo_external==null)
        $user->is_photo_external = false;


        if(!$is_social || ($user->id && !$user->is_social_login)){

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            // Auto-verify and activate regular registrations
            $user->verification_token = null;
            $user->is_verified = 1;
            $user->status = 1;
            $user->email_verified_at = Carbon::now();
        }
        else{

            //for login
            if(!$user->id){
                $existing_user = $this->find_by_email($request->email,true);
                if($existing_user)
                    return $existing_user;
            }

            //else, it's signup or account edit
             
            $user->is_social_login    = 1;
            $user->social_provider    = $request->social_provider;
            $user->is_photo_external  = false;
            
            // Auto-verify and activate social logins
            if (!$user->email_verified_at) {
                $user->email_verified_at = Carbon::now();
            }
            $user->is_verified = 1;
            $user->status = 1;
            $user->verification_token = null;

            if($request->photo){
                $user->photo = $request->photo;
                $user->is_photo_external = true;
            }

        }
        
        if($request->hasFile( 'photo')):
            $file = $request->file('photo');
            $extension = strtolower((string) ($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg'));
            $file_name = md5_file($file->getRealPath());
            $file_path = $file_name.'.'.$extension;
            $storagePath = hub_storage_path('uploads/users');
            if (! is_dir($storagePath)) {
                mkdir($storagePath, 0755, true);
            }
            $file->move($storagePath, $file_path);
            $user->photo  = $file_path;
            $user->is_photo_external  = false;
        endif;

        // Handle subscription preference
        if($request->has('is_subscribed')) {
            $user->is_subscribed = $request->is_subscribed ? true : false;
        } elseif($request->subscribe) {
            $user->is_subscribed = ($request->subscribe=="on")?true:false;
        }

        if($request->author_id)
        $user->author_id= $request->author_id;

        // Save language preference - always update if provided (even if empty string)
        if($request->has('langauge')) {
            $user->langauge = $request->langauge;
        }

        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'theme_preference') && $request->has('theme_preference')) {
            $user->theme_preference = in_array($request->theme_preference, ['light', 'dark', 'system']) ? $request->theme_preference : 'light';
        }

        if ($request->has('level_id')) {
            $actor = auth()->user();
            if ($actor && $actor->can('alter_access_levels')) {
                $levelId = $request->input('level_id');
                $user->access_level_id = ($levelId === null || $levelId === '') ? null : (int) $levelId;
            }
        }

        if (! $user->exists && ! $user->created_at) {
            $user->created_at = Carbon::now();
        }

        $user_saved = ($user->id)?$user->update():$user->save();
        $user = User::find($user->id);

        if ($user) {
            try {
                if ((! $user->author_id || ! Author::find((int) $user->author_id)) && ! $request->filled('author_id')) {
                    app(\App\Repositories\AuthorsRepository::class)->ensureAuthorForUser($user);
                    $user->refresh();
                }
            } catch (\Throwable $ex) {
                \Log::error('Error creating author for user', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $ex->getMessage(),
                ]);
                // Fail registration loudly; don't break profile/OAuth updates.
                if (! $request->filled('id') && ! $is_social) {
                    throw $ex;
                }
            }
        }

        // Email verification is no longer needed since we auto-verify
        // Removed: if(!$is_social) $this->send_email($request, $token);

        if($request->preferences){

            $user->preferences()->delete();
            @$this->save_preferences($user->id,$request->preferences);
        }

        if($request->communities){

            //$user->communities()->delete();
            //@$this->save_communities($user->id,$request->communities);
        }
        
        // Auto-enroll Africa CDC staff based on email domain
        try{
            if ($user && !empty($user->email) && preg_match('/@africacdc\.org$/i', $user->email)) {
                $africaCDCCommunityId = 31; // Africa CDC Staff
                $exists = CommunityOfPracticeMembers::where('user_id', $user->id)
                    ->where('community_of_practice_id', $africaCDCCommunityId)
                    ->exists();
                if (!$exists) {
                    $member = new CommunityOfPracticeMembers();
                    $member->user_id = $user->id;
                    $member->community_of_practice_id = $africaCDCCommunityId;
                    $member->is_approved = 1;
                    $member->save();
                }
            }
        }catch(\Exception $e){
            \Log::warning('Auto-enroll Africa CDC Staff failed', ['user_id'=>$user->id ?? null, 'error'=>$e->getMessage()]);
        }
        
        return $user;
    }

    public function send_email($request, $token){

        $mail['subject'] = 'Account confirmation';
        $mail['email'] = $request->email;
        $mail['body'] = view('emails.email_verification', ['token' => $token])->render();

        SendMailJob::dispatch($mail)->onQueue('default');
    }

    public function save_preferences($user_id,$preferences){

        $preferences = is_array($preferences) ? $preferences : (json_decode($preferences) ?? []);

        foreach($preferences as $subtheme_id){

            $pref = new UserPreference();
            $pref->user_id = $user_id;
            $pref->subtheme_id  = $subtheme_id;
            $pref->save();

        }

    }

    public function save_communities($user_id,$communities){

        $communities = is_array($communities) ? $communities : (json_decode($communities) ?? []);

        foreach($communities as $community_id){

            $comm = new CommunityOfPracticeMembers();
            $comm->user_id = $user_id;
            $comm->community_of_practice_id  = $community_id;
            $comm->save();

        }

    }

    public function user_by_token($token){

        return User::where('verification_token',$token)->first();
    }

    public function verify_account($request){
        
        $user = $this->user_by_token($request->t);

        if($user):
            $user->is_verified = 1;
            $user->status = 1; // Activate the account
            $user->verification_token = null; // Clear the token
            $user->email_verified_at = Carbon::now();
            return $user->update();
        else:
            return null;
        endif;
    }

    public function update_profile(Request $request){
        return $this->save($request);
    }

    public function update_password(Request $request){

        $user = User::find(current_user()->id);
        $newpass = Hash::make($request->password);
        $user->password = $newpass;
        $user->is_changed = 1;

        return $user->update();
    }

    public function logout(User $user)
    {
        // Revoke all tokens for the user
        $user->tokens->each(function ($token) {
            $token->revoke();
        });

        // Clear the FCM token
        $user->fcm_token = null;
        $user->save();

        return true;
    }

    public function find_by_email($email,$as_social=false){
       
        $qry = User::where('email',$email);
        
        if($as_social):
            $qry->where('is_social_login',true);
        else:
            $qry->where('is_social_login',false);
        endif;

        $user = $qry->first();
        return $user;
    }


  


}
