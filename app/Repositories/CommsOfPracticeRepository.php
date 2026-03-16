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

        // Filter by is_public for public-facing requests (not admin)
        // Admin can see all communities
        if (!$request->has('admin')) {
            $query->where('is_public', 1);
        }

        // Add search functionality
        if ($request->filled('term')) {
            $term = $request->input('term');
            $query->where(function($q) use ($term) {
                $q->where('community_name', 'like', '%' . $term . '%')
                  ->orWhere('description', 'like', '%' . $term . '%');
            });
        }

        // Filter by coverage type
        // Whole of Africa = region_id is null AND country_id is null
        // Region = region_id is set AND country_id is null
        // Country = country_id is set
        if ($request->filled('coverage')) {
            $coverage = $request->input('coverage');
            if ($coverage === 'whole_of_africa') {
                $query->whereNull('region_id')->whereNull('country_id');
            } elseif ($coverage === 'region') {
                $query->whereNotNull('region_id')->whereNull('country_id');
            } elseif ($coverage === 'country') {
                $query->whereNotNull('country_id');
            }
        }

        // Filter by region_id
        if ($request->filled('region_id')) {
            $query->where('region_id', $request->input('region_id'));
        }

        // Filter by country_id
        if ($request->filled('country_id')) {
            $query->where('country_id', $request->input('country_id'));
        }

        // Filter by organisation
        if ($request->filled('organisation')) {
            $query->where('organisation', 'like', '%' . $request->input('organisation') . '%');
        }

        // Filter by department
        if ($request->filled('department')) {
            $query->where('department', 'like', '%' . $request->input('department') . '%');
        }

        if ($request->input('withRelated', false)) {
            $query->with(['membership', 'approvedMembers','approvedMembers.user', 'pendingMembers', 'rejectedMembers', 'communityForums', 'communityPublications', 'region', 'country']);
        } else {
            // Always load region and country for displaying coverage info
            $query->with(['region', 'country']);
        }

        $results = $return_array ? $query->get() : $query->paginate($request->rows ?? 20);
        
        // Append query parameters to pagination links
        if (!$return_array) {
            $appends = array_filter($request->only(['term', 'coverage', 'region_id', 'country_id', 'organisation', 'department']));
            if (!empty($appends)) {
                $results->appends($appends);
            }
        }
        
        return $results;
    }

    /**
     * Search public communities by term for the records search page (combined with publications and forums).
     */
    public function searchForRecords(Request $request, $limit = 5)
    {
        if (!$request->filled('term') || strlen(trim($request->term)) === 0) {
            return collect();
        }
        $term = trim($request->term);
        return CommunityOfPractice::with(['region', 'country'])
            ->where('is_public', 1)
            ->where(function ($q) use ($term) {
                $q->where('community_name', 'like', '%' . $term . '%')
                  ->orWhere('description', 'like', '%' . $term . '%')
                  ->orWhere('organisation', 'like', '%' . $term . '%')
                  ->orWhere('department', 'like', '%' . $term . '%');
            })
            ->orderBy('community_name')
            ->limit($limit)
            ->get();
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
        
        // Handle region - if "all" is selected, set to null
        if ($request->has('region_id') && $request->region_id === 'all') {
            $access_grp->region_id = null;
        } else {
            $access_grp->region_id = $request->region_id ?: null;
        }
        
        // Handle country - if empty or "all" is selected, set to null
        if (!$request->has('country_id') || $request->country_id === '' || $request->country_id === null) {
            $access_grp->country_id = null;
        } else {
            $access_grp->country_id = $request->country_id;
        }
        
        $access_grp->organisation = $request->organisation ?: null;
        $access_grp->department = $request->department ?: null;
        $access_grp->is_public = $request->has('is_public') ? (bool)$request->is_public : true;
        
        $access_grp->save();

        // Handle tags
        if ($request->has('tags') && is_array($request->tags)) {
            // Sync tags (remove old ones, add new ones)
            $access_grp->tags()->sync($request->tags);
        } else {
            // If no tags provided, remove all tags
            $access_grp->tags()->sync([]);
        }

        clear_cache();
        
        return $access_grp;
    }

    public function find($id, $withRelated = false)
    {
        $query = CommunityOfPractice::where('id', $id);

        if ($withRelated) {
            $query->with(['membership', 'approvedMembers', 'pendingMembers', 'rejectedMembers', 'communityForums', 'communityPublications', 'tags']);
        } else {
            // Always load tags for edit forms
            $query->with('tags');
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

        try {
            \App\Jobs\SendMailJob::dispatch($emailData)->onQueue('default');
        } catch (\Throwable $e) {
            \Log::error('COP invitation email failed', [
                'community_id' => $communityId,
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
            return [
                'status' => 'error',
                'message' => 'Invitation was created but the email could not be sent: ' . $e->getMessage(),
                'data' => $invitation,
            ];
        }

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
     * Resend an invitation (new token, new expiry, send email again).
     * Only for invitations that have not been responded to.
     */
    public function resendInvitation($invitationId, $communityId, $invitedBy)
    {
        $invitation = CommunityInvitation::where('id', $invitationId)
            ->where('community_of_practice_id', $communityId)
            ->whereNull('responded_at')
            ->first();

        if (!$invitation) {
            return ['status' => 'error', 'message' => 'Invitation not found or already used.'];
        }

        $invitation->update([
            'token' => CommunityInvitation::generateToken(),
            'expires_at' => now()->addDays(7),
            'invited_by' => $invitedBy,
        ]);
        $invitation->load(['community', 'inviter']);

        $acceptUrl = url('/communities/accept-invitation/' . $invitation->token);
        $subject = 'Reminder: Invitation to Join: ' . $invitation->community->community_name;
        $body = view('emails.community_invitation', [
            'invitation' => $invitation,
            'community' => $invitation->community,
            'inviterName' => $invitation->inviter->name ?? 'Administrator',
            'acceptUrl' => $acceptUrl,
        ])->render();

        $emailData = (object) [
            'email' => $invitation->email,
            'subject' => $subject,
            'body' => $body,
            'title' => $subject
        ];

        try {
            \App\Jobs\SendMailJob::dispatch($emailData)->onQueue('default');
        } catch (\Throwable $e) {
            \Log::error('COP resend invitation email failed', ['invitation_id' => $invitationId, 'error' => $e->getMessage()]);
            return ['status' => 'error', 'message' => 'Email could not be sent: ' . $e->getMessage()];
        }

        return ['status' => 'success', 'message' => 'Invitation resent successfully.', 'data' => $invitation];
    }

    /**
     * Send invitations to multiple emails; skip already members or existing pending invitations.
     * Returns summary: sent, skipped_member, skipped_pending, invalid.
     */
    public function sendInvitationsBulk($communityId, array $emails, $invitedBy)
    {
        $emails = array_unique(array_map('strtolower', array_map('trim', $emails)));
        $sent = 0;
        $skippedMember = 0;
        $skippedPending = 0;
        $invalid = 0;
        $errors = [];

        foreach ($emails as $email) {
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $invalid++;
                continue;
            }

            $existingMember = CommunityOfPracticeMembers::where('community_of_practice_id', $communityId)
                ->whereHas('user', function ($q) use ($email) {
                    $q->where('email', $email);
                })
                ->first();
            if ($existingMember) {
                $skippedMember++;
                continue;
            }

            $existingInvitation = CommunityInvitation::where('community_of_practice_id', $communityId)
                ->where('email', $email)
                ->whereNull('responded_at')
                ->where('expires_at', '>', now())
                ->first();
            if ($existingInvitation) {
                $skippedPending++;
                continue;
            }

            $result = $this->sendInvitation($communityId, $email, $invitedBy);
            if ($result['status'] === 'success') {
                $sent++;
            } else {
                $errors[] = $email . ': ' . ($result['message'] ?? 'Failed');
            }
        }

        return [
            'sent' => $sent,
            'skipped_member' => $skippedMember,
            'skipped_pending' => $skippedPending,
            'invalid' => $invalid,
            'errors' => $errors,
        ];
    }

    /**
     * Parse CSV file and return array of email addresses (first column or column named email).
     */
    public function parseEmailsFromCsv($file)
    {
        $emails = [];
        $path = $file->getRealPath();
        $handle = fopen($path, 'r');
        if (!$handle) {
            return $emails;
        }
        $header = fgetcsv($handle);
        $emailIndex = 0;
        if ($header !== false) {
            $emailIndex = array_search('email', array_map('strtolower', array_map('trim', $header)));
            if ($emailIndex === false) {
                $emailIndex = 0;
            }
        }
        while (($row = fgetcsv($handle)) !== false) {
            if (isset($row[$emailIndex]) && trim($row[$emailIndex]) !== '') {
                $emails[] = trim($row[$emailIndex]);
            }
        }
        fclose($handle);
        return array_unique($emails);
    }

    /**
     * Bulk invite from CSV: for each community, send to emails that are not already members and have no active invitation.
     * $scope: 'this' | 'all' | 'selected'. For 'selected', $communityIds must be provided.
     */
    public function bulkInviteFromCsv($scope, array $communityIds, array $emails, $invitedBy)
    {
        if ($scope === 'all') {
            $communityIds = CommunityOfPractice::pluck('id')->toArray();
        } elseif ($scope === 'selected' && empty($communityIds)) {
            return ['error' => 'No communities selected.'];
        }

        $emails = array_unique(array_filter(array_map(function ($e) {
            $e = strtolower(trim($e));
            return filter_var($e, FILTER_VALIDATE_EMAIL) ? $e : null;
        }, $emails)));

        if (empty($emails)) {
            return ['error' => 'No valid emails in the file.'];
        }

        $totalSent = 0;
        $totalSkippedMember = 0;
        $totalSkippedPending = 0;
        $perCommunity = [];

        foreach ($communityIds as $cid) {
            $result = $this->sendInvitationsBulk($cid, $emails, $invitedBy);
            $totalSent += $result['sent'];
            $totalSkippedMember += $result['skipped_member'];
            $totalSkippedPending += $result['skipped_pending'];
            $perCommunity[$cid] = $result;
        }

        return [
            'sent' => $totalSent,
            'skipped_member' => $totalSkippedMember,
            'skipped_pending' => $totalSkippedPending,
            'invalid' => count($emails) * count($communityIds) - $totalSent - $totalSkippedMember - $totalSkippedPending,
            'per_community' => $perCommunity,
            'communities_count' => count($communityIds),
        ];
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
