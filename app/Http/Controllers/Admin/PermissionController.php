<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Str;
use App\Jobs\SendMailJob;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditTrail;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\Utils;
use App\Http\Controllers\Controller;
use App\Models\AccessLevel;
use App\Repositories\SharedRepo;
use App\Repositories\UsersRepository;
use App\Repositories\LogsRepository;

class PermissionController extends Controller
{
   
    private $sharedRepo; private $usersRepo; private $logsRepo;

    public function __construct(SharedRepo $sharedRepo, UsersRepository $usersRepo, LogsRepository $logsRepo)
    {
        $this->sharedRepo = $sharedRepo;
        $this->usersRepo  = $usersRepo;
        $this->logsRepo   = $logsRepo;
    }

    /*
        Renders a list of all roles
    */
    public function index(){

        $data['roles']  = Role::paginate(15);
        $data['permissions'] = Permission::all();
        return view('admin.permissions.roles')->with($data);
        
    }

    /*
        Renders a list of all users
    */
    public function users(Request $request)
    {
        $data['roles'] = Role::all();
        // Prefer unique level names so duplicate seeded rows (Viewer/Country/…) do not confuse the UI.
        $data['levels'] = AccessLevel::query()
            ->orderBy('id')
            ->get()
            ->unique(function ($level) {
                return strtolower((string) $level->level_name);
            })
            ->values();
        
        // Load countries and authors for dropdowns
        $data['countries'] = \App\Models\Country::orderBy('name')->get();
        $data['authors'] = \App\Models\Author::orderBy('name')->get();

        $name = $request->filled('term') ? urldecode((string) $request->term) : null;
        $country_id = $request->country_id;
        $phone = $request->mobile;
        $count = (! empty($request->count)) ? (int) $request->count : 20;
        $isStaff = $request->has('is_staff') ? $request->boolean('is_staff') : null;
        $verifiedFilter = $request->input('verified');

        $accessLogSub = '(SELECT user_id, MAX(created_at) AS access_last_at FROM access_logs WHERE user_id IS NOT NULL AND user_id != \'\' GROUP BY user_id) AS al';
        $spatieUserSql = 'App\\Models\\User';

        $usersQuery = DB::table('users')
            ->leftJoin('country', 'users.country_id', '=', 'country.id')
            ->leftJoin('access_levels', 'users.access_level_id', '=', 'access_levels.id')
            ->leftJoin('author', 'users.author_id', '=', 'author.id')
            ->leftJoin(DB::raw($accessLogSub), function ($join) {
                $join->whereRaw('CAST(al.user_id AS UNSIGNED) = users.id');
            })
            ->select(
                'users.id', 'users.name', 'users.first_name', 'users.last_name', 'users.email', 'users.phone_number', 'users.status',
                'users.email_verified_at', 'users.is_social_login', 'users.social_provider',
                'users.country_id', 'users.administrative_unit_id', 'users.author_id', 'users.access_level_id',
                'users.job_title', 'users.photo', 'users.is_photo_external',
                'users.last_login_at',
                'country.name as country_name',
                'access_levels.level_name as access_level_name',
                'al.access_last_at',
                'author.name as author_name',
                DB::raw("(SELECT r.id FROM model_has_roles mhr INNER JOIN roles r ON r.id = mhr.role_id WHERE mhr.model_id = users.id AND mhr.model_type = '".$spatieUserSql."' ORDER BY r.id ASC LIMIT 1) AS role_id"),
                DB::raw("(SELECT r.name FROM model_has_roles mhr INNER JOIN roles r ON r.id = mhr.role_id WHERE mhr.model_id = users.id AND mhr.model_type = '".$spatieUserSql."' ORDER BY r.id ASC LIMIT 1) AS role_name")
            )
            ->when($phone, function ($query, $phone) {
                return $query->where('users.phone_number', 'like', $phone.'%');
            })
            ->when($name, function ($query, $name) {
                return $query->where(function ($query) use ($name) {
                    $query->where('users.name', 'like', '%'.$name.'%')
                        ->orWhere('users.email', 'like', '%'.$name.'%')
                        ->orWhere('users.phone_number', 'like', '%'.$name.'%');
                });
            })
            ->when($country_id, function ($query, $country_id) {
                return $query->where('users.country_id', $country_id);
            })
            ->when($isStaff === true, function ($query) {
                return $query->where('users.email', 'like', '%@africacdc.org');
            })
            ->when($isStaff === false, function ($query) {
                return $query->where('users.email', 'not like', '%@africacdc.org');
            })
            ->when($verifiedFilter === '1' || $verifiedFilter === 1, function ($query) {
                return $query->whereNotNull('users.email_verified_at');
            })
            ->when($verifiedFilter === '0' || $verifiedFilter === 0, function ($query) {
                return $query->whereNull('users.email_verified_at');
            })
            ->when($request->input('missing_author') === '1', function ($query) {
                return $query->where(function ($q) {
                    $q->whereNull('users.author_id')
                        ->orWhere('users.author_id', 0)
                        ->orWhereNull('author.id');
                });
            })
            ->when(true, function ($query) {
                return $this->sharedRepo->access_filter($query, true, true);
            })
            ->orderBy('users.name', 'asc');

        $users = $usersQuery->paginate($count);
        $users->appends($request->query());

        $data['users'] = $users;

        $data['search'] = (object) [
            'country_id' => $country_id,
            'name' => $name,
            'term' => $request->input('term'),
            'count' => $count,
            'phone' => $phone,
            'is_staff' => $request->input('is_staff'),
            'verified' => $verifiedFilter,
            'missing_author' => $request->input('missing_author'),
        ];

        $data['usersMissingAuthorCount'] = User::query()
            ->leftJoin('author', 'users.author_id', '=', 'author.id')
            ->where(function ($q) {
                $q->whereNull('users.author_id')
                    ->orWhere('users.author_id', 0)
                    ->orWhereNull('author.id');
            })
            ->count('users.id');

        return view('admin.permissions.users')->with($data);
    }

    /**
     * Create/link author accounts for users that registered without one.
     */
    public function ensureAuthorAccounts(Request $request)
    {
        $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $authorsRepo = app(\App\Repositories\AuthorsRepository::class);

        try {
            if ($request->filled('user_id')) {
                $user = User::findOrFail((int) $request->user_id);
                $authorsRepo->ensureAuthorForUser($user);
                $message = 'Author account assigned for '.$user->name.'.';
                $alertClass = 'success';
            } else {
                $result = $authorsRepo->assignMissingAuthorAccounts();
                $message = 'Assigned author accounts to '.$result['assigned'].' user(s).'
                    .($result['failed'] ? ' Failed: '.$result['failed'].'.' : '');
                $alertClass = $result['failed'] ? 'warning' : 'success';
            }
        } catch (\Throwable $e) {
            report($e);
            $message = 'Could not assign author account(s): '.$e->getMessage();
            $alertClass = 'danger';
        }

        if ($request->ajax()) {
            return response()->json([
                'status' => $alertClass === 'danger' ? 'error' : 'success',
                'message' => $message,
                'alert_class' => $alertClass,
            ], $alertClass === 'danger' ? 500 : 200);
        }

        return back()->with([
            'message' => $message,
            'alert' => $message,
            'alert_class' => $alertClass,
        ]);
    }

    public function sendVerification(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $user = \App\Models\User::find($request->id);
        if(!$user){
            notify()->error('User not found');
            return back()->with(['alert'=>'User not found','alert_class'=>'danger']);
        }
        // generate / refresh token
        $token = Str::random(10);
        $user->verification_token = $token;
        $user->save();
        // build temp request for existing mailer
        // Queue verification email on default queue for cron workers
        $mail = [
            'subject' => 'Account verification',
            'email'   => $user->email,
                'body'    => view('emails.simple', [
                    'title' => 'Verify Your Account',
                    'content' => "Hello {$user->name},<br><br>Please verify your email using this token:<br><strong>{$token}</strong><br><br>You can use this token to verify your account on the verification page.",
                    'buttonText' => 'Verify Account',
                    'buttonUrl' => url('account/verify?token=' . $token),
                ])->render(),
        ];
        $queued = false;
        try { SendMailJob::dispatch($mail)->onQueue('default'); $queued = true; } catch (\Throwable $e) { $queued = false; }
        if(!$queued){
            try{
                // Fallback to direct mail pipeline already used elsewhere
                $req = new \Illuminate\Http\Request();
                $req->merge(['email' => $user->email]);
                $this->usersRepo->send_email($req, $token);
                notify()->success('Verification email sent (fallback)');
                return back()->with(['alert'=>'Verification email sent (fallback)','alert_class'=>'success']);
            }catch(\Throwable $e){
                notify()->error('Failed to queue or send verification email');
                return back()->with(['alert'=>'Failed to queue or send verification email','alert_class'=>'danger']);
            }
        }
        notify()->success('Verification email queued');
        return back()->with(['alert'=>'Verification email queued','alert_class'=>'success']);
    }

    public function verifyUser(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $user = User::find($request->id);
        if(!$user){
            notify()->error('User not found');
            return back()->with(['alert'=>'User not found','alert_class'=>'danger']);
        }
        $user->is_verified = 1;
        if (empty($user->email_verified_at)) {
            $user->email_verified_at = now();
        }
        $saved = $user->save();
        if($saved){ notify()->success('User verified'); }
        return back()->with(['alert'=>'User verified','alert_class'=>'success']);
    }

    /*
        Renders a list of all permissions
    */
    public function permissions(){
        $data['permissions'] = Permission::paginate(15);
        return view('admin.permissions.permissions')->with($data);
    }

    public function saveUser(Request $request){

        //dd($request->all());
        $user = new User();

        $lastName       = $request->last_name;
        $firstName      = $request->first_name;
        $nin       = $request->nin;
        $email     = $request->email;
        $mobile    = $request->mobile;
        $roleId    = $request->role_id;
        $password  = (empty($request->pass) && !$request->id)?Str::random(6):$request->pass;
        

        if($request->id)
         $user = User::find($request->id);

        $country_id = self::resolveUserCountryIdForSave(
            $request->filled('country_id') ? (int) $request->country_id : null
        );
        $accessLevelId = self::resolveAccessLevelIdForSave($request, $user->access_level_id ?? null);

        $user->first_name = $firstName;
        $user->last_name  = $lastName;
        $user->country_id  = $country_id;
        $user->phone_number    = $mobile;
        $user->email     = $email;
        $user->name      = $lastName." ".$firstName;
        $user->author_id =  $request->author_id;
        $user->access_level_id =  $accessLevelId;
        $user->administrative_unit_id =  $request->administrative_unit_id;
        if($request->has('status')){ $user->status = (int)$request->status; }
        if($request->has('is_verified')){ $user->is_verified = (int)!!$request->is_verified; if($user->is_verified && empty($user->email_verified_at)){ $user->email_verified_at = now(); } }
       
        if(!empty($password)){
          $user->password  = Hash::make($password);
          $user->pwd_changed = 0;
        }

        $saved = ($request->id)? $user->update():$user->save();

        if ($saved) {
            try {
                if (! $user->author_id || ! \App\Models\Author::find((int) $user->author_id)) {
                    app(\App\Repositories\AuthorsRepository::class)->ensureAuthorForUser($user->fresh());
                }
            } catch (\Throwable $e) {
                \Log::error('Admin saveUser author ensure failed', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }

            $isViewer = $this->isViewerAccessLevel($request->level_id ?? null);
            if ($isViewer) {
                $user->syncRoles([]);
            } elseif ($roleId) {
                $user->syncRoles([]);
                $user->assignRole($roleId);
            }
        }

        $msg = (!$saved)?"Operation failed, try again":($request->id?"User <b> $user->name </b> updated successfully":"User <b> $user->name </b> created successfuly with default password <b> $password </b>");
       
        $alert_class = ($saved)?'success':'danger';
        $alert = ['alert-'.$alert_class=>$msg];
        return back()->with($alert);
    }

    public function changePassword(Request $request){

        if(isset($request->password)){

            $validator = $request->validate([
                'password' => 'required|min:6',
                'confirmpassword' => 'required',
            ]);

            $user = Auth::user();
            $password = $request->password;
            $confirm = $request->confirmpassword;
    
            if($password == $confirm ):

                $user->password    = Hash::make($password);
                $user->pwd_changed = 1;
                $user->update();
        
                return redirect()->route('home');
            else:
                //return redirect()->route('home');
            endif;
        }
       return view('auth.changepass');
    }

    /*
        Create roles
    */
    public function saveRole(Request $request){

        $role_name  = $request->get('role_name');
        $role_id    = $request->get('rowid'); //edits

        $role = null;
       
        if($role_id):
         $role = Role::find($role_id);
        else:
         $role = new Role();
        endif;

        $role->name =$role_name;
        $saved = $role->save();
        $msg = (!$saved)?"Operation failed, try again":"Role saved successfuly,refresh to view changes";
        $data["message"] = $msg;
        $data['data'] = ($saved)?$role:[];
        
        $alert_class = ($saved)?'success':'danger';
        $alert = ['alert-'.$alert_class=>$msg];

        return back()->with($alert);
    }

    /*
        Create permissions
    */
    public function createPermission(Request $request){

        $perm_name = $request->get('perm_name');
        $perm_desc = $request->get('description');
        $perm_id   = $request->get('id');

        $perm = null;
       
        if($perm_id):
         $perm = Permission::find($perm_id);
        else:
         $perm = new Permission();
        endif;
        $perm->name = $perm_name;
        $perm->description = $perm_desc;

        $saved = $perm->save();
        $msg = (!$saved)?"Operation failed, try again":"Permission saved successfuly,refresh to view changes";
        $data["message"] = $msg;
        $data['data'] = ($saved)?$perm:[];
        
        $alert_class = ($saved)?'success':'danger';
        $alert = ['alert-'.$alert_class=>$msg];

        return back()->with($alert);
    }

    /*
        Assign permissions to roles
    */
    public function permissionsToRole(Request $request){

        $roledId     = $request->get('role_id');
        $permissions = ($request->get('permissions'))?$request->get('permissions'):[];
        $role = Role::findById($roledId);
        $new_permissions= Permission::whereIn('id',$permissions)->get();
        $saved  = $role->syncPermissions($new_permissions);

        $msg = (!$saved)?"Operation failed, try again":"Assigned successfuly,refresh to view changes";
        $data["message"] = $msg;
        $data['data'] = $permissions;

        $alert_class = ($saved)?'success':'danger';

        $alert = ['alert-'.$alert_class=>$msg];

        return redirect()->route('permissions.roles')->with($alert);
    }

    /*
        Revoke permissions from roles
    */
    public function revokePermissions(Request $request){

        $roleId = $request->get('role');
        $permissions = $request->get('permissions');
        $role = Role::findById($roleId);
        $saved = false;

        foreach($permissions as $perm):
            $permission= Permission::where_in('id',$perm)->get();
            $saved = $role->revokePermissionTo($permission);
        endforeach;

        $msg = (!$saved)?"Operation failed, try again":"Revoked successfuly,refresh to view changes";
        $data["message"] = $msg;
        $data['data']    = [];

        $alert_class = ($saved)?'success':'danger';
        $alert       = ['alert-'.$alert_class=>$msg];

        return redirect()->route('permissions.roles')->with($alert);
    }

    public function roleToUser(Request $request){

        $userId = $request->user_id;
        $roleId = $request->role_id;

        $user   = User::find($userId);
        $old_data = $user;

        $accessLevelId = self::resolveAccessLevelIdForSave($request, $user->access_level_id);
        $isViewer = $this->isViewerAccessLevel($accessLevelId);

        $user->access_level_id = $accessLevelId;
        $user->author_id       = $request->author_id;
        $user->country_id      = self::resolveUserCountryIdForSave(
            $request->filled('country_id') ? (int) $request->country_id : ($user->country_id ? (int) $user->country_id : null)
        );
        $user->update();

        $user->syncRoles([]);
        $saved = (!$isViewer && $roleId)
            ? $user->assignRole($roleId)
            : true;

        $msg = (!$saved)?"Operation failed, try again":"Role assigned successfuly,refresh to view changes";
    
        $data["message"] = $msg;
        $data['data'] = [$userId,$roleId];
        $alert_class = ($saved)?'success':'danger';

        log_user_trail('UPDATED',"Updated User $userId Details",$old_data,$user);

        $alert = ['alert-'.$alert_class=>$msg];

        return redirect()->route('permissions.users')->with($alert);
    }

    /**
     * Country hubs always store the configured owner country on the user account.
     */
    public static function resolveUserCountryIdForSave(?int $requestCountryId): ?int
    {
        if (function_exists('hub_is_country_portal') && hub_is_country_portal()) {
            $ownerId = function_exists('hub_owner_country_id') ? hub_owner_country_id() : null;
            if ($ownerId) {
                return (int) $ownerId;
            }
        }

        return $requestCountryId;
    }

    /**
     * Keep the existing access level when the form omits level_id (e.g. older country-hub layouts).
     */
    public static function resolveAccessLevelIdForSave(Request $request, $existingLevelId = null): ?int
    {
        if (! $request->exists('level_id')) {
            return $existingLevelId !== null && $existingLevelId !== ''
                ? (int) $existingLevelId
                : null;
        }

        $levelId = $request->input('level_id');

        return ($levelId !== null && $levelId !== '') ? (int) $levelId : null;
    }

    /*
        Create roles
    */
    public function resetUser(Request $request){

        $password = Str::random(10);
        $user_id  = $request->input('id', $request->input('user_id'));
        $status   = (int) $request->input('status', 3); // default: Reset

        $user = User::find($user_id);
        if(!$user){
            notify()->error('User not found');
            return back()->with(['alert'=>'User not found','alert_class'=>'danger']);
        }

        $user->status      = $status;
        $user->pwd_changed = 0;
        if(in_array($status, [1,3])){ // activating or resetting
            $user->password = Hash::make($password);
        }

        $saved = $user->save();

        if($saved){
            // email the new password to the user
            $mail = [
                'subject' => 'Your account password has been reset',
                'email'   => $user->email,
                'body'    => view('emails.password_changed', [
                    'name' => $user->name,
                    'password' => $password,
                ])->render(),
            ];
            $queued = false; try{ SendMailJob::dispatch($mail)->onQueue('default'); $queued = true; }catch(\Throwable $e){ $queued = false; }
            if(!$queued){
                try{
                    $req = new \Illuminate\Http\Request();
                    $req->merge(['email' => $user->email]);
                    $this->usersRepo->send_email($req, $password);
                }catch(\Throwable $e){}
            }
        }

        $msg = (!$saved)?'Operation failed, try again':"User <b>{$user->name}</b> has been reset. Temporary password: <b>{$password}</b>";
        $alert_class = ($saved)?'success':'danger';

        if($saved){ notify()->success('Password reset email queued and temporary password generated.'); }
        else { notify()->error('Operation failed, please try again.'); }
        return redirect()->route('permissions.users')->with(['alert'=>$msg,'alert_class'=>$alert_class]);
    }


    public function deleteUser(Request $request){

        // Accept either 'id' (from modal) or 'user_id'
        $user_id = $request->input('id', $request->input('user_id'));
        $user    = User::find($user_id);
        $deleted = $user ? $user->delete() : false;

        $msg = (!$deleted)?"Operation failed, try again":"User <b> $user->name </b>, with username <b>$user->email</b> has been  <b> deleted</b>";
        $alert_class = ($deleted)?'success':'danger';
        $alert = ['alert-'.$alert_class=>$msg];
        if($deleted){ notify()->success('User deleted successfully'); } else { notify()->error('Delete failed'); }
        return back()->with($alert);
    }

    public function profile(Request $request){

        $data['user']        = User::find($request->user) ?? current_user();
        $data['preferences'] = [];
        $data['access_groups'] = AccessLevel::all();

        $currentUser = current_user();
        if ($currentUser) {
            foreach($currentUser->preferences as $pref){
                $data['preferences'][] = $pref->subtheme_id;
            }
        }
        
        return view('admin.profile.index')->with($data);
    }

    public function trail(Request $request)
    {
        log_user_trail('Accessed',$description=null);

        $data['users']  = User::all();
        $data['search'] = (Object) $request->all();
        $data['trails'] = $this->logsRepo->audit_trail($request);

        return view('admin.permissions.audit')->with($data);
    }

    private function isViewerAccessLevel($levelId): bool
    {
        if (empty($levelId)) {
            return false;
        }
        $level = AccessLevel::find($levelId);

        return $level && strcasecmp((string) $level->level_name, 'Viewer') === 0;
    }

}
