<?php
namespace App\Repositories;

use App\Models\CommunityOfPractice;
use App\Models\CommunityOfPracticeMembers;
use App\Models\CommunityInvitation;
use App\Models\Tag;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CommsOfPracticeRepository{

    /**
     * Hide "Africa CDC Staff" from listings/API unless the viewer is logged in with an @africacdc.org email.
     * Admin requests (admin=true) always see all communities.
     */
    private function scopeAfricaCdcStaffVisibility($query, Request $request): void
    {
        if ($request->boolean('admin')) {
            return;
        }
        if (user_email_allows_africa_cdc_staff_community(auth()->user())) {
            return;
        }
        $query->whereRaw('LOWER(TRIM(community_name)) != ?', [strtolower(community_africa_cdc_staff_name())]);
    }

    public function get(Request $request, $return_array = false)
    {
        $query = CommunityOfPractice::query();

        // Filter by is_public for public-facing requests (not admin)
        // Admin can see all communities
        if (!$request->has('admin')) {
            $query->where('is_public', 1);
        }

        $this->scopeAfricaCdcStaffVisibility($query, $request);

        // Add search functionality (community name, description, or creator name/email)
        if ($request->filled('term')) {
            $term = $request->input('term');
            $query->where(function($q) use ($term) {
                $q->where('community_name', 'like', '%' . $term . '%')
                  ->orWhere('description', 'like', '%' . $term . '%')
                  ->orWhereHas('creator', function($q2) use ($term) {
                      $q2->where('name', 'like', '%' . $term . '%')
                         ->orWhere('email', 'like', '%' . $term . '%');
                  });
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
            $query->with(['membership', 'approvedMembers','approvedMembers.user', 'pendingMembers', 'rejectedMembers', 'communityForums', 'communityPublications', 'region', 'country', 'tags']);
        } else {
            // Always load region, country, and tags for listing cards
            $query->with(['region', 'country', 'tags']);
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
        $q = CommunityOfPractice::with(['region', 'country'])
            ->where('is_public', 1)
            ->where(function ($q2) use ($term) {
                $q2->where('community_name', 'like', '%' . $term . '%')
                  ->orWhere('description', 'like', '%' . $term . '%')
                  ->orWhere('organisation', 'like', '%' . $term . '%')
                  ->orWhere('department', 'like', '%' . $term . '%');
            });
        if (! user_email_allows_africa_cdc_staff_community(auth()->user())) {
            $q->whereRaw('LOWER(TRIM(community_name)) != ?', [strtolower(community_africa_cdc_staff_name())]);
        }

        return $q->orderBy('community_name')
            ->limit($limit)
            ->get();
    }

    public function getByUser($userId, Request $request)
    {
        // Get communities where user is an approved member
        $memberCommunityIds = CommunityOfPracticeMembers::where('user_id', $userId)
            ->where('is_approved', 1)
            ->where('is_active', 1)
            ->pluck('community_of_practice_id');

        $query = CommunityOfPractice::whereIn('id', $memberCommunityIds);

        $memberUser = User::find($userId);
        if (! user_email_allows_africa_cdc_staff_community($memberUser)) {
            $query->whereRaw('LOWER(TRIM(community_name)) != ?', [strtolower(community_africa_cdc_staff_name())]);
        }

        // Add search functionality
        if ($request->filled('term')) {
            $term = $request->input('term');
            $query->where(function($q) use ($term) {
                $q->where('community_name', 'like', '%' . $term . '%')
                  ->orWhere('description', 'like', '%' . $term . '%');
            });
        }

        if ($request->input('withRelated', false)) {
            $query->with(['membership', 'approvedMembers','approvedMembers.user', 'pendingMembers', 'rejectedMembers', 'communityForums', 'communityPublications', 'region', 'country', 'tags']);
        } else {
            $query->with(['region', 'country', 'tags']);
        }

        $results = $query->paginate($request->rows ?? 20);
        
        // Append query parameters to pagination links
        if ($request->filled('term')) {
            $results->appends($request->only(['term']));
        }
        
        return $results;
    }

    /**
     * Suggested communities for a logged-in user: match profile sub-themes and tags from favourited publications,
     * then fall back to largest public communities the user has not joined.
     *
     * @return \Illuminate\Support\Collection<int, CommunityOfPractice>
     */
    public function recommendedForUser(int $userId, int $limit = 9): Collection
    {
        $joinedIds = CommunityOfPracticeMembers::query()
            ->where('user_id', $userId)
            ->pluck('community_of_practice_id')
            ->unique()
            ->filter()
            ->values()
            ->all();

        $keywords = collect();
        $user = User::query()->with(['preferences.subtheme'])->find($userId);
        if ($user) {
            foreach ($user->preferences as $pref) {
                if ($pref->subtheme) {
                    $st = $pref->subtheme;
                    foreach (['description', 'detailed_description'] as $attr) {
                        if (! empty($st->{$attr})) {
                            $text = strip_tags((string) $st->{$attr});
                            $keywords = $keywords->merge(preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY));
                        }
                    }
                }
            }

            $favTagIds = DB::table('favourites')
                ->join('publication_tags', 'favourites.publication_id', '=', 'publication_tags.publication_id')
                ->where('favourites.user_id', $userId)
                ->distinct()
                ->pluck('publication_tags.tag_id');

            if ($favTagIds->isNotEmpty()) {
                $keywords = $keywords->merge(Tag::query()->whereIn('id', $favTagIds)->pluck('tag_text'));
            }
        }

        $keywords = $keywords->map(function ($k) {
            $k = strtolower(trim((string) $k));

            return preg_replace('/^[^\p{L}\p{N}]+|[^\p{L}\p{N}]+$/u', '', $k);
        })->filter(function ($k) {
            return strlen($k) >= 3;
        })->unique()->take(25)->values()->all();

        $allowStaffCommunity = $user && user_email_allows_africa_cdc_staff_community($user);

        $baseQuery = function () use ($joinedIds, $allowStaffCommunity) {
            $q = CommunityOfPractice::query()
                ->where('is_public', 1)
                ->with(['region', 'country', 'tags']);
            if ($joinedIds !== []) {
                $q->whereNotIn('id', $joinedIds);
            }
            if (! $allowStaffCommunity) {
                $q->whereRaw('LOWER(TRIM(community_name)) != ?', [strtolower(community_africa_cdc_staff_name())]);
            }

            return $q;
        };

        if ($keywords !== []) {
            $matched = $baseQuery()->where(function ($q) use ($keywords) {
                foreach ($keywords as $kw) {
                    $safe = addcslashes($kw, '%_\\');
                    $q->orWhere(function ($sub) use ($safe) {
                        $sub->where('community_name', 'like', '%'.$safe.'%')
                            ->orWhere('description', 'like', '%'.$safe.'%')
                            ->orWhereHas('tags', function ($tq) use ($safe) {
                                $tq->where('tag_text', 'like', '%'.$safe.'%');
                            });
                    });
                }
            })
                ->limit($limit * 4)
                ->get()
                ->unique('id')
                ->take($limit)
                ->values();

            if ($matched->isNotEmpty()) {
                return $matched;
            }
        }

        return $baseQuery()
            ->withCount('approvedMembers')
            ->orderByDesc('approved_members_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Last activity, admin + top contributors (max 8 faces), and “+N” member overflow for listing cards.
     *
     * @param  LengthAwarePaginator|Collection  $communities
     */
    public function attachListingMeta($communities): void
    {
        $items = [];
        if ($communities instanceof LengthAwarePaginator) {
            $items = $communities->items();
        } elseif ($communities instanceof Collection) {
            $items = $communities->all();
        }
        if ($items === []) {
            return;
        }

        $ids = collect($items)->pluck('id')->filter()->unique()->values()->all();
        if ($ids === []) {
            return;
        }

        $maxFaces = 8;

        $activityRows = DB::table('forum_community_of_practices as fcp')
            ->join('forums as f', 'f.id', '=', 'fcp.forum_id')
            ->whereIn('fcp.community_of_practice_id', $ids)
            ->groupBy('fcp.community_of_practice_id')
            ->selectRaw('fcp.community_of_practice_id as cid, MAX(f.created_at) as last_at')
            ->pluck('last_at', 'cid');

        $memberGroups = CommunityOfPracticeMembers::query()
            ->whereIn('community_of_practice_id', $ids)
            ->where('is_approved', 1)
            ->with(['user' => function ($q) {
                $q->select('id', 'name', 'photo', 'updated_at', 'job_title', 'is_photo_external');
            }])
            ->orderByDesc('id')
            ->get()
            ->groupBy('community_of_practice_id');

        $adminByCid = CommunityOfPracticeMembers::query()
            ->whereIn('community_of_practice_id', $ids)
            ->where('is_approved', 1)
            ->where('is_admin', 1)
            ->orderByDesc('id')
            ->get(['community_of_practice_id', 'user_id'])
            ->groupBy('community_of_practice_id');

        $links = DB::table('forum_community_of_practices')
            ->whereIn('community_of_practice_id', $ids)
            ->get(['community_of_practice_id', 'forum_id']);

        $forumToCids = [];
        foreach ($links as $l) {
            $fid = (int) $l->forum_id;
            $cid = (int) $l->community_of_practice_id;
            if (! isset($forumToCids[$fid])) {
                $forumToCids[$fid] = [];
            }
            $forumToCids[$fid][$cid] = true;
        }
        foreach ($forumToCids as $fid => $cmap) {
            $forumToCids[$fid] = array_keys($cmap);
        }

        $allForumIds = array_keys($forumToCids);
        $scoresByCid = [];
        foreach ($ids as $id) {
            $scoresByCid[(int) $id] = [];
        }

        if ($allForumIds !== []) {
            $forumCreators = DB::table('forums')->whereIn('id', $allForumIds)->pluck('created_by', 'id');

            foreach ($forumToCids as $fid => $cids) {
                $creator = $forumCreators[$fid] ?? null;
                if (! $creator) {
                    continue;
                }
                $uid = (int) $creator;
                foreach ($cids as $cid) {
                    $scoresByCid[$cid][$uid] = ($scoresByCid[$cid][$uid] ?? 0) + 5;
                }
            }

            $commentAgg = DB::table('forum_comments')
                ->whereIn('forum_id', $allForumIds)
                ->whereNotNull('created_by')
                ->where(function ($q) {
                    $q->where('status', 'approved')
                        ->orWhere('status', 1)
                        ->orWhereNull('status');
                })
                ->groupBy('forum_id', 'created_by')
                ->selectRaw('forum_id, created_by as user_id, COUNT(*) as cnt')
                ->get();

            foreach ($commentAgg as $row) {
                $fid = (int) $row->forum_id;
                $uid = (int) $row->user_id;
                $cnt = (int) $row->cnt;
                foreach ($forumToCids[$fid] ?? [] as $cid) {
                    $scoresByCid[$cid][$uid] = ($scoresByCid[$cid][$uid] ?? 0) + $cnt;
                }
            }
        }

        $slotsByCid = [];
        $allFaceUserIds = [];

        foreach ($items as $c) {
            if (! $c instanceof CommunityOfPractice) {
                continue;
            }
            $cid = (int) $c->id;
            $ordered = [];
            $seen = [];

            $push = function (int $uid, string $role) use (&$ordered, &$seen, $maxFaces) {
                if ($uid <= 0 || isset($seen[$uid]) || count($ordered) >= $maxFaces) {
                    return;
                }
                $seen[$uid] = true;
                $ordered[] = ['user_id' => $uid, 'role' => $role];
            };

            if (! empty($c->created_by)) {
                $push((int) $c->created_by, 'creator');
            }

            $admins = $adminByCid[$cid] ?? $adminByCid[(string) $cid] ?? collect();
            foreach ($admins as $adm) {
                $push((int) $adm->user_id, 'admin');
            }

            $scores = $scoresByCid[$cid] ?? [];
            arsort($scores);
            foreach (array_keys($scores) as $uid) {
                $push((int) $uid, 'contributor');
            }

            $members = $memberGroups[$cid] ?? $memberGroups[(string) $cid] ?? collect();
            foreach ($members as $m) {
                if ($m->user_id) {
                    $push((int) $m->user_id, 'member');
                }
            }

            $slotsByCid[$cid] = $ordered;
            foreach ($ordered as $slot) {
                $allFaceUserIds[$slot['user_id']] = true;
            }
        }

        $userById = collect();
        if ($allFaceUserIds !== []) {
            $userById = User::query()
                ->whereIn('id', array_keys($allFaceUserIds))
                ->get(['id', 'name', 'photo', 'updated_at', 'job_title', 'is_photo_external'])
                ->keyBy('id');
        }

        $onlineBefore = Carbon::now()->subMinutes(20);

        foreach ($items as $c) {
            if (! $c instanceof CommunityOfPractice) {
                continue;
            }
            $cid = (int) $c->id;
            $raw = $activityRows[$cid] ?? $activityRows[(string) $cid] ?? null;
            $c->setAttribute('listing_last_activity', $raw ? Carbon::parse($raw) : null);

            $faces = collect();
            $memberIdSet = [];
            $mg = $memberGroups[$cid] ?? $memberGroups[(string) $cid] ?? collect();
            foreach ($mg as $mem) {
                if ($mem->user_id) {
                    $memberIdSet[(int) $mem->user_id] = true;
                }
            }

            foreach ($slotsByCid[$cid] ?? [] as $slot) {
                $u = $userById->get($slot['user_id']);
                if (! $u) {
                    continue;
                }
                $online = $u->updated_at && $u->updated_at->gt($onlineBefore);
                $faces->push([
                    'user' => $u,
                    'role' => $slot['role'],
                    'online' => $online,
                ]);
            }

            $c->setAttribute('listing_contributor_faces', $faces);

            $shownMemberCount = $faces->filter(function ($f) use ($memberIdSet) {
                return isset($memberIdSet[(int) $f['user']->id]);
            })->count();

            $c->setAttribute('listing_more_members_not_shown', max(0, (int) ($c->members_count ?? 0) - $shownMemberCount));

            // Backward compatibility for any code using listing_member_preview
            $c->setAttribute('listing_member_preview', $mg->take(8));
        }
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
            $member->is_active = 1;
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
        } elseif ($action === 'activate') {
            $member->is_active = 1;
            $member->save();
        } elseif ($action === 'deactivate') {
            $member->is_active = 0;
            $member->save();
        } elseif ($action === 'make_admin') {
            $member->is_admin = 1;
            $member->save();
        } elseif ($action === 'remove_admin') {
            $member->is_admin = 0;
            $member->save();
        }

        return $member;
    }

    public function addMember($communityId, $userId) {
        $community = CommunityOfPractice::find($communityId);
        if ($community && community_is_africa_cdc_staff_restricted($community)) {
            $joinUser = User::find($userId);
            if (! $joinUser || ! user_email_allows_africa_cdc_staff_community($joinUser)) {
                return false;
            }
        }

        $existing = CommunityOfPracticeMembers::where('community_of_practice_id', $communityId)
            ->where('user_id', $userId)
            ->first();
        if ($existing) {
            return $existing;
        }

        CommunityOfPracticeMembers::create([
            'community_of_practice_id' => $communityId,
            'user_id' => $userId,
            'is_approved' => 0,
            'is_active' => 1,
            'is_admin' => 0,
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

        $community = CommunityOfPractice::find($communityId);
        if ($community && community_is_africa_cdc_staff_restricted($community)) {
            if (! preg_match('/@africacdc\.org$/i', trim((string) $email))) {
                return ['status' => 'error', 'message' => 'This community is limited to @africacdc.org email addresses.'];
            }
        }

        try {
            // Create new invitation
            $invitation = CommunityInvitation::create([
                'community_of_practice_id' => $communityId,
                'email' => $email,
                'token' => CommunityInvitation::generateToken(),
                'invited_by' => $invitedBy,
                'expires_at' => now()->addDays(7),
            ]);
        } catch (\Throwable $e) {
            \Log::error('CommunityInvitation::create failed', [
                'community_id' => $communityId,
                'email' => $email,
                'invited_by' => $invitedBy,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return ['status' => 'error', 'message' => 'Could not create invitation: ' . $e->getMessage()];
        }

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

        // Use plain array so queue serialization preserves recipient; 'email' and 'to' = invitee
        $emailData = [
            'email' => $email,
            'to' => $email,
            'subject' => $subject,
            'body' => $body,
            'title' => $subject,
        ];

        try {
            // Send invitation email synchronously so it reaches the invitee immediately (no queue worker required)
            \App\Jobs\SendMailJob::dispatchSync($emailData);
            \Log::info('COP invitation email sent to invitee', ['to' => $email, 'community_id' => $communityId]);
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

        $community = CommunityOfPractice::find($invitation->community_of_practice_id);
        if ($community && community_is_africa_cdc_staff_restricted($community) && ! user_email_allows_africa_cdc_staff_community($user)) {
            return ['status' => 'error', 'message' => 'This community is only available to users with an @africacdc.org email address.'];
        }

        // Add user to community with auto-approval
        DB::transaction(function() use ($invitation, $user) {
            CommunityOfPracticeMembers::create([
                'community_of_practice_id' => $invitation->community_of_practice_id,
                'user_id' => $user->id,
                'is_approved' => 1, // Auto-approve invited members
                'is_active' => 1,
                'is_admin' => 0,
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

        $to = $invitation->email;
        $emailData = [
            'email' => $to,
            'to' => $to,
            'subject' => $subject,
            'body' => $body,
            'title' => $subject,
        ];

        try {
            \App\Jobs\SendMailJob::dispatchSync($emailData);
            \Log::info('COP resend invitation email sent to invitee', ['to' => $to, 'invitation_id' => $invitationId]);
        } catch (\Throwable $e) {
            \Log::error('COP resend invitation email failed', ['invitation_id' => $invitationId, 'to' => $to, 'error' => $e->getMessage()]);
            return ['status' => 'error', 'message' => 'Email could not be sent: ' . $e->getMessage()];
        }

        return ['status' => 'success', 'message' => 'Invitation resent successfully.', 'data' => $invitation];
    }

    /**
     * Delete a community invitation (any status: pending, expired, or responded).
     */
    public function deleteInvitation($invitationId, $communityId)
    {
        $invitation = CommunityInvitation::where('id', $invitationId)
            ->where('community_of_practice_id', $communityId)
            ->first();

        if (!$invitation) {
            return ['status' => 'error', 'message' => 'Invitation not found.'];
        }

        $invitation->delete();
        return ['status' => 'success', 'message' => 'Invitation deleted.'];
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
