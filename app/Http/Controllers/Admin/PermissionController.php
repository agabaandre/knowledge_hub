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
        $data['levels'] = AccessLevel::all();
        
        // Load countries and authors for dropdowns
        $data['countries'] = \App\Models\Country::orderBy('name')->get();
        $data['authors'] = \App\Models\Author::orderBy('name')->get();

        $name = urldecode($request->term);
        $country_id = $request->country_id;
        $phone = $request->mobile;
        $count = (!empty($request->count)) ? $request->count : 20;
        $isStaff = $request->boolean('is_staff', null);
        $verified = $request->input('verified', null); // '1' or '0'

        $usersQuery = DB::table('users')
            ->leftJoin('country', 'users.country_id', '=', 'country.id')
            ->leftJoin('model_has_roles as mhr', function($join){
                $join->on('mhr.model_id', '=', 'users.id')
                     ->where('mhr.model_type', '=', 'App\\Models\\User');
            })
            ->leftJoin('roles', 'roles.id', '=', 'mhr.role_id')
            ->leftJoin(DB::raw('(select user_id, max(created_at) as last_login_at from access_logs group by user_id) as al'), 'al.user_id', '=', 'users.id')
            ->select(
                'users.id','users.name','users.first_name','users.last_name','users.email','users.phone_number','users.status',
                'users.email_verified_at','users.is_social_login','users.social_provider',
                'users.country_id','users.administrative_unit_id','users.author_id','users.access_level_id',
                'country.name as country_name','roles.name as role_name','roles.id as role_id','al.last_login_at'
            )
            ->when($phone, function ($query, $phone) {
                return $query->where('users.mobile', 'like', $phone . '%');
            })
            ->when($name, function ($query, $name) {
                return $query->where(function ($query) use ($name) {
                    $query->where('users.name', 'like', $name . '%')
                        ->orWhere('users.email', 'like', $name . '%')
                        ->orWhere('users.phone_number', 'like', $name . '%');
                });
            })
            ->when($country_id, function ($query, $country_id) {
                return $query->where('users.country_id', $country_id);
            })
            ->when(true, function ($query) {
                return $this->sharedRepo->access_filter($query, true, true);
            })
            ->orderBy('users.name', 'desc');

        // For DataTables client-side mode, return all rows
        if ($request->ajax()) {
            $users = $usersQuery->get();
        } else {
            $users = $usersQuery->paginate($count);
            $users->appends($request->all());
        }

        // If AJAX request, return a lightweight JSON array for DataTables
        if ($request->ajax()) {
            $rows = [];
            $index = 1;
            $statuses = [0=>'InActive',2=>'Restricted',3=>'Reset',1=>'Active'];
            foreach ($users as $u) {
                $verified = $u->email_verified_at ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-secondary">No</span>';
                $statusBadge = '<span class="badge '.($u->status==1?'badge-success':'badge-secondary').'">'.($statuses[$u->status] ?? 'InActive').'</span>';
                $type = $u->is_social_login ? '<span class="badge badge-info">Social</span>' : '<span class="badge badge-light">Normal</span>';
                $lastLogin = $u->last_login_at ? date('M d, Y H:i', strtotime($u->last_login_at)) : '-';

                $actions = '<div class="btn-group btn-group-sm" role="group">'
                    .'<button type="button" class="btn btn-outline-primary btn-edit-user" data-id="'.$u->id.'" data-first-name="'.e($u->first_name ?? '').'" data-last-name="'.e($u->last_name ?? '').'" data-name="'.e($u->name ?? '').'" data-email="'.e($u->email ?? '').'" data-phone="'.e($u->phone_number ?? '').'" data-role-id="'.($u->role_id ?? '').'" data-role="'.e($u->role_name ?? '').'" data-country-id="'.($u->country_id ?? '').'" data-administrative-unit-id="'.($u->administrative_unit_id ?? '').'" data-author-id="'.($u->author_id ?? '').'" data-level-id="'.($u->access_level_id ?? '').'" data-verified="'.($u->email_verified_at?1:0).'" data-status="'.(int)$u->status.'">'
                    .'<i class="fa fa-edit"></i> Edit</button>'
                    .'<button type="button" class="btn btn-outline-warning btn-reset-user" data-id="'.$u->id.'"><i class="fa fa-key"></i> Reset</button>'
                    .(!$u->email_verified_at ? '<button type="button" class="btn btn-outline-success btn-send-verify" data-id="'.$u->id.'"><i class="fa fa-paper-plane"></i> Email Verify</button>' : '')
                    .'<button type="button" class="btn btn-outline-danger btn-delete-user" data-id="'.$u->id.'"><i class="fa fa-trash"></i> Delete</button>'
                    .'</div>';

                $contact = '<div style="line-height:1.2">'
                    .'<div><i class="fa fa-envelope mr-1"></i>'.e($u->email ?? '').'</div>'
                    .'<div><i class="fa fa-phone mr-1"></i>'.e($u->phone_number ?? '-').'</div>'
                    .'<div><i class="fa fa-globe mr-1"></i>'.e($u->country_name ?? '-').'</div>'
                    .'</div>';

                $rows[] = [
                    $index++,
                    e($u->name ?? ''),
                    $contact,
                    $verified,
                    $statusBadge,
                    $type,
                    $lastLogin,
                    strtoupper($u->role_name ?? 'N/A'),
                    $actions,
                ];
            }
            return response()->json(['data' => $rows]);
        }

        $data['users']  = $users;


        $data['search'] = (object) array(
            "country_id"=>$country_id,
            "name"=>$name,
            "count" =>$count,
            "phone" =>$phone
        );


        return view('admin.permissions.users')->with($data);
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
        $country_id  = $request->country_id;
        $nin       = $request->nin;
        $email     = $request->email;
        $mobile    = $request->mobile;
        $roleId    = $request->role_id;
        $password  = (empty($request->pass) && !$request->id)?Str::random(6):$request->pass;
        

        if($request->id)
         $user = User::find($request->id);

        $user->first_name = $firstName;
        $user->last_name  = $lastName;
        $user->country_id  = $country_id;
        $user->phone_number    = $mobile;
        $user->email     = $email;
        $user->name      = $lastName." ".$firstName;
        $user->author_id =  $request->author_id;
        $user->access_level_id =  $request->level_id;
        $user->administrative_unit_id =  $request->administrative_unit_id;
        if($request->has('status')){ $user->status = (int)$request->status; }
        if($request->has('is_verified')){ $user->is_verified = (int)!!$request->is_verified; if($user->is_verified && empty($user->email_verified_at)){ $user->email_verified_at = now(); } }
       
        if(!empty($password)){
          $user->password  = Hash::make($password);
          $user->pwd_changed = 0;
        }

        $saved = ($request->id)? $user->update():$user->save();

        if ($saved) {
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
        $isViewer = $this->isViewerAccessLevel($request->level_id ?? null);

        $user   = User::find($userId);
        $old_data = $user;

        $user->access_level_id = $request->level_id;
        $user->author_id       = $request->author_id;
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
