<?php
namespace App\Repositories;

use App\Models\CommunityOfPractice;
use App\Models\CommunityOfPracticeMembers;
use App\Models\CommunityInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommsOfPracticeRepository{

    public function get(Request $request, $return_array = false)
    {
        $query = CommunityOfPractice::query();

        // Add search functionality
        if ($request->filled('term')) {
            $term = $request->input('term');
            $query->where(function($q) use ($term) {
                $q->where('community_name', 'like', '%' . $term . '%')
                  ->orWhere('description', 'like', '%' . $term . '%');
            });
        }

        if ($request->input('withRelated', false)) {
            $query->with(['membership', 'approvedMembers','approvedMembers.user', 'pendingMembers', 'rejectedMembers', 'communityForums', 'communityPublications']);
        }

        $results = $return_array ? $query->get() : $query->paginate($request->rows ?? 20);
        
        // Append query parameters to pagination links
        if (!$return_array && $request->filled('term')) {
            $results->appends($request->only(['term']));
        }
        
        return $results;
    }

    public function getByUser($userId, Request $request)
    {
        // Get communities where user is an approved member
        $memberCommunityIds = CommunityOfPracticeMembers::where('user_id', $userId)
            ->where('is_approved', 1)
            ->pluck('community_of_practice_id');

        $query = CommunityOfPractice::whereIn('id', $memberCommunityIds);

        // Add search functionality
        if ($request->filled('term')) {
            $term = $request->input('term');
            $query->where(function($q) use ($term) {
                $q->where('community_name', 'like', '%' . $term . '%')
                  ->orWhere('description', 'like', '%' . $term . '%');
            });
        }

        if ($request->input('withRelated', false)) {
            $query->with(['membership', 'approvedMembers','approvedMembers.user', 'pendingMembers', 'rejectedMembers', 'communityForums', 'communityPublications']);
        }

        $results = $query->paginate($request->rows ?? 20);
        
        // Append query parameters to pagination links
        if ($request->filled('term')) {
            $results->appends($request->only(['term']));
        }
        
        return $results;
    }
    
    public function save(Request $request){

        $access_grp = ($request->id)?CommunityOfPractice::find($request->id):new CommunityOfPractice();

        $access_grp->community_name = clean_unicode($request->community_name ?? '');
        $access_grp->description = clean_unicode($request->description ?? '');
        $access_grp->created_by = current_user()->id;
        $access_grp->save();

        clear_cache();
        
        return $access_grp;
    }

    public function find($id, $withRelated = false)
    {
        $query = CommunityOfPractice::where('id', $id);

        if ($withRelated) {
            $query->with(['membership', 'approvedMembers', 'pendingMembers', 'rejectedMembers', 'communityForums', 'communityPublications']);
        }

        return $query->first();
    }

   function delete($id)
    {
        
        $deleted = CommunityOfPractice::find($id)->delete();
        clear_cache();
        return $deleted;
    }
    
    public function getAllWithMembership()
    {
        return CommunityOfPractice::with([
            'membership',
            'approvedMembers',
            'pendingMembers',
            'rejectedMembers'
        ])->get();
    }

    public function updateMemberStatus($memberId, $action) {
       
        $member = CommunityOfPracticeMembers::with(['user', 'community'])->find($memberId);

        
        if ($action === 'approve') {
            $member->is_approved = 1;
            $member->save();
            
            // Send approval email notification to the member
            if ($member->user && $member->user->email && $member->community) {
                $subject = 'Community Membership Approved: ' . $member->community->community_name;
                
                $body = view('emails.community_membership_approved', [
                    'memberName' => $member->user->name ?? 'Member',
                    'communityName' => $member->community->community_name,
                    'communityDescription' => $member->community->description ?? '',
                    'communityUrl' => url('communities') . '?term=' . urlencode($member->community->community_name),
                ])->render();

                $emailData = (object) [
                    'email' => $member->user->email,
                    'subject' => $subject,
                    'body' => $body,
                    'title' => $subject
                ];

                \App\Jobs\SendMailJob::dispatch($emailData)->onQueue('default');
            }
        } elseif ($action === 'reject') {
            $member->is_approved = 2; // Set to 2 for rejected
            $member->save();
        }

        return $member;
    }

    public function addMember($communityId, $userId) {
       
        CommunityOfPracticeMembers::create([
            'community_of_practice_id' => $communityId,
            'user_id' => $userId,
            'is_approved' => 0,
        ]);

        return true; // or any relevant response
    }

    public function removeMember($communityId, $userId) {
        $member = CommunityOfPracticeMembers::where('community_of_practice_id', $communityId)
                                            ->where('user_id', $userId)
                                            ->first();
        if ($member) {
            $member->delete();
            return true;
        }
        return false;
    }

    public function getPaginatedForums($communityId, $perPage = 10)
    {
        return CommunityOfPractice::find($communityId)->communityForums()->paginate($perPage);
    }

    public function getPaginatedPublications($communityId, $perPage = 10)
    {
        return CommunityOfPractice::find($communityId)->communityPublications()->paginate($perPage);
    }

    public function sendMessage(Request $request)
    {
        $communityIds = $request->input('community_ids', []);
        $memberIds = $request->input('member_ids', []);
        $message = $request->input('message');
        $title = $request->input('title');

        $message = str_replace('{name}', 'Member', $message);

        foreach ($communityIds as $communityId) {
            $community = CommunityOfPractice::with('approvedMembers.user')->find($communityId);

            if (!$community) {
                continue; // Skip if community not found
            }

            $members = $community->approvedMembers;
            $fcmTokens = [];

            if (empty($memberIds)) {
                // Collect FCM tokens for all approved members if no specific members are selected
                foreach ($members as $member) {
                    if ($member->user->fcm_token) { // Assuming 'fcm_token' is the field name
                        $fcmTokens[] = $member->user->fcm_token;
                    }
                }
            } else {
                // Collect FCM tokens for specific members
                foreach ($members as $member) {
                    if (in_array($member->user_id, $memberIds) && $member->user->fcm_token) {
                        $fcmTokens[] = $member->user->fcm_token;
                    }
                }
            }

            // Call the helper function to send the push notification
            if (!empty($fcmTokens)) {
                sendPushNotification($title, $message, $fcmTokens);
            }
        }

        return true; // Indicate success
    }

    /**
     * Send invitation to join community
     */
    public function sendInvitation($communityId, $email, $invitedBy)
    {
        // Check if user is already a member
        $existingMember = CommunityOfPracticeMembers::where('community_of_practice_id', $communityId)
            ->whereHas('user', function($query) use ($email) {
                $query->where('email', $email);
            })
            ->first();

        if ($existingMember) {
            return ['status' => 'error', 'message' => 'User is already a member of this community'];
        }

        // Check if there's already a pending invitation for this email and community
        $existingInvitation = CommunityInvitation::where('community_of_practice_id', $communityId)
            ->where('email', $email)
            ->whereNull('responded_at')
            ->where('expires_at', '>', now())
            ->first();

        if ($existingInvitation) {
            return ['status' => 'error', 'message' => 'An active invitation already exists for this email'];
        }

        // Create new invitation
        $invitation = CommunityInvitation::create([
            'community_of_practice_id' => $communityId,
            'email' => $email,
            'token' => CommunityInvitation::generateToken(),
            'invited_by' => $invitedBy,
            'expires_at' => now()->addDays(7),
        ]);

        // Load relationships for email
        $invitation->load(['community', 'inviter']);

        // Send invitation email via queue system (which uses Exchange)
        $acceptUrl = url('/communities/accept-invitation/' . $invitation->token);
        $subject = 'Invitation to Join: ' . $invitation->community->community_name;
        
        $body = view('emails.community_invitation', [
            'invitation' => $invitation,
            'community' => $invitation->community,
            'inviterName' => $invitation->inviter->name ?? 'Administrator',
            'acceptUrl' => $acceptUrl,
        ])->render();

        $emailData = (object) [
            'email' => $email,
            'subject' => $subject,
            'body' => $body,
            'title' => $subject
        ];

        \App\Jobs\SendMailJob::dispatch($emailData)->onQueue('default');

        return ['status' => 'success', 'message' => 'Invitation sent successfully', 'data' => $invitation];
    }

    /**
     * Accept invitation and add user to community
     */
    public function acceptInvitation($token)
    {
        $invitation = CommunityInvitation::where('token', $token)->first();

        if (!$invitation) {
            return ['status' => 'error', 'message' => 'Invalid invitation token'];
        }

        if ($invitation->isExpired()) {
            return ['status' => 'error', 'message' => 'This invitation has expired'];
        }

        if ($invitation->isResponded()) {
            return ['status' => 'error', 'message' => 'This invitation has already been used'];
        }

        // Find user by email
        $user = \App\Models\User::where('email', $invitation->email)->first();

        if (!$user) {
            return ['status' => 'error', 'message' => 'No account found with this email. Please register first.'];
        }

        // Check if user is already a member
        $existingMember = CommunityOfPracticeMembers::where('community_of_practice_id', $invitation->community_of_practice_id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingMember) {
            // Mark invitation as responded even if already member
            $invitation->markAsResponded();
            return ['status' => 'error', 'message' => 'You are already a member of this community'];
        }

        // Add user to community with auto-approval
        DB::transaction(function() use ($invitation, $user) {
            CommunityOfPracticeMembers::create([
                'community_of_practice_id' => $invitation->community_of_practice_id,
                'user_id' => $user->id,
                'is_approved' => 1, // Auto-approve invited members
            ]);

            // Mark invitation as responded
            $invitation->markAsResponded();
        });

        return [
            'status' => 'success',
            'message' => 'You have successfully joined the community',
            'community' => $invitation->community
        ];
    }

    /**
     * Get invitations for a community
     */
    public function getInvitations($communityId)
    {
        return CommunityInvitation::where('community_of_practice_id', $communityId)
            ->with('inviter')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Delete expired invitations
     */
    public function pruneExpiredInvitations()
    {
        $deleted = CommunityInvitation::where('expires_at', '<', now())
            ->whereNull('responded_at')
            ->delete();

        return $deleted;
    }
}
