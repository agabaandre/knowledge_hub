<?php
namespace App\Repositories;

use App\Models\CommunityOfPractice;
use App\Models\CommunityOfPracticeMembers;
use App\Models\CommunityComment;
use App\Models\CommunityCommentLike;
use App\Models\CommunityInvitation;
use App\Models\CustomAttachment;
use App\Models\Event;
use App\Models\Forum;
use App\Models\ForumCommunityOfPractice;
use App\Models\Publication;
use App\Models\PublicationCommunityOfPractice;
use App\Models\Tag;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Services\OfficeDocumentToPdfService;
use App\Support\SeoSlugger;

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

    /**
     * For @africacdc.org users, list “Africa CDC Staff” before other communities (secondary sort by name).
     */
    private function orderAfricaCdcStaffCommunityFirstForViewer($query, $viewer): void
    {
        if (! user_email_allows_africa_cdc_staff_community($viewer)) {
            return;
        }
        $query->orderByRaw(
            'CASE WHEN LOWER(TRIM(community_name)) = ? THEN 0 ELSE 1 END',
            [strtolower(community_africa_cdc_staff_name())]
        );
    }

    /**
     * Logged-in members see communities they belong to before the rest of the directory.
     */
    private function orderMemberCommunitiesFirstForViewer($query, $viewer): void
    {
        if (! $viewer || ! auth()->check()) {
            return;
        }

        $userId = (int) auth()->id();
        $query->orderByRaw(
            'CASE WHEN EXISTS (
                SELECT 1 FROM community_of_practice_members AS copm
                WHERE copm.community_of_practice_id = community_of_practices.id
                  AND copm.user_id = ?
                  AND copm.is_approved = 1
                  AND copm.is_active = 1
            ) THEN 0 ELSE 1 END',
            [$userId]
        );
    }

    /**
     * @return array{joined: int, pending: int}
     */
    public function membershipStatsForUser(int $userId): array
    {
        $base = CommunityOfPracticeMembers::query()
            ->where('user_id', $userId)
            ->whereHas('community', function ($q) {
                $q->where('is_public', 1);
            });

        return [
            'joined' => (clone $base)->where('is_approved', 1)->where('is_active', 1)->count(),
            'pending' => (clone $base)->where('is_approved', 0)->count(),
        ];
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

        // Unified search (name, description, coverage, region, country, organisation, department)
        if ($request->filled('term')) {
            $term = trim((string) $request->input('term'));
            if (mb_strlen($term) >= 4) {
                $this->applyListingTermSearch($query, $term);
            }
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

        $minMembers = (int) $request->input('min_members', 0);
        if ($minMembers > 0) {
            $query->withCount([
                'membership as approved_active_members_count' => function ($q) {
                    $q->where('community_of_practice_members.is_approved', 1)
                        ->where('community_of_practice_members.is_active', 1);
                },
            ])->having('approved_active_members_count', '>=', $minMembers);
        }

        if ($request->input('withRelated', false)) {
            $query->with(['membership', 'approvedMembers', 'approvedMembers.user', 'pendingMembers', 'rejectedMembers', 'communityForums', 'communityPublications', 'region', 'country', 'tags', 'creator']);
        } else {
            $query->with(['region', 'country', 'tags', 'creator']);
        }

        if (! $request->boolean('admin')) {
            $this->orderMemberCommunitiesFirstForViewer($query, auth()->user());
            $this->orderAfricaCdcStaffCommunityFirstForViewer($query, auth()->user());
        }
        $query->orderBy('community_name');

        $results = $return_array ? $query->get() : $query->paginate($request->rows ?? 20);
        
        // Append query parameters to pagination links
        if (!$return_array) {
            $appends = array_filter($request->only(['term', 'coverage', 'region_id', 'country_id', 'organisation', 'department', 'min_members']));
            if (!empty($appends)) {
                $results->appends($appends);
            }
        }
        
        return $results;
    }

    /**
     * Match communities against a single search term (coverage, geography, org, etc.).
     */
    protected function applyListingTermSearch($query, string $term): void
    {
        $like = '%'.$term.'%';
        $termLower = mb_strtolower($term);

        $query->where(function ($q) use ($like, $termLower) {
            $q->where('community_name', 'like', $like)
                ->orWhere('description', 'like', $like)
                ->orWhere('organisation', 'like', $like)
                ->orWhere('department', 'like', $like)
                ->orWhereHas('creator', function ($q2) use ($like) {
                    $q2->where('name', 'like', $like)
                        ->orWhere('email', 'like', $like);
                })
                ->orWhereHas('region', function ($q2) use ($like) {
                    $q2->where('region_name', 'like', $like);
                })
                ->orWhereHas('country', function ($q2) use ($like) {
                    $q2->where('name', 'like', $like);
                })
                ->orWhereHas('membership', function ($q2) use ($like) {
                    $q2->where('is_approved', 1)
                        ->where('is_admin', 1)
                        ->whereHas('user', function ($q3) use ($like) {
                            $q3->where('name', 'like', $like)
                                ->orWhere('job_title', 'like', $like);
                        });
                });

            if (str_contains($termLower, 'whole of africa')
                || ($termLower === 'whole')
                || ($termLower === 'africa' && ! str_contains($termLower, 'cdc'))) {
                $q->orWhere(function ($q2) {
                    $q2->whereNull('region_id')->whereNull('country_id');
                });
            }
        });
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

        $this->orderAfricaCdcStaffCommunityFirstForViewer($query, $memberUser);
        $query->orderBy('community_name');

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
     * Last activity, admin + top contributors (face count from settings), and “+N” member overflow for listing cards.
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

        $maxFaces = function_exists('communities_listing_show_participants') && function_exists('communities_listing_max_faces')
            ? (communities_listing_show_participants() ? communities_listing_max_faces() : 0)
            : 8;

        $activityRows = DB::table('forum_community_of_practices as fcp')
            ->join('forums as f', 'f.id', '=', 'fcp.forum_id')
            ->whereIn('fcp.community_of_practice_id', $ids)
            ->groupBy('fcp.community_of_practice_id')
            ->selectRaw('fcp.community_of_practice_id as cid, MAX(f.created_at) as last_at')
            ->pluck('last_at', 'cid');

        $adminByCid = CommunityOfPracticeMembers::query()
            ->whereIn('community_of_practice_id', $ids)
            ->where('is_approved', 1)
            ->where('is_admin', 1)
            ->orderBy('id')
            ->get(['community_of_practice_id', 'user_id'])
            ->groupBy('community_of_practice_id');

        $this->attachListingChairs($items, $adminByCid);

        if ($maxFaces === 0) {
            foreach ($items as $c) {
                if (! $c instanceof CommunityOfPractice) {
                    continue;
                }
                $cid = (int) $c->id;
                $raw = $activityRows[$cid] ?? $activityRows[(string) $cid] ?? null;
                $c->setAttribute('listing_last_activity', $raw ? Carbon::parse($raw) : null);
                $c->setAttribute('listing_contributor_faces', collect());
                $c->setAttribute('listing_more_members_not_shown', 0);
                $c->setAttribute('listing_member_preview', collect());
            }

            return;
        }

        $memberGroups = CommunityOfPracticeMembers::query()
            ->whereIn('community_of_practice_id', $ids)
            ->where('is_approved', 1)
            ->with(['user' => function ($q) {
                $q->select('id', 'name', 'photo', 'updated_at', 'job_title', 'is_photo_external');
            }])
            ->orderByDesc('id')
            ->get()
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
                ->get(['id', 'name', 'photo', 'updated_at', 'job_title', 'is_photo_external', 'author_id'])
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
            $c->setAttribute('listing_member_preview', $mg->take($maxFaces));
        }
    }

    /**
     * Creator + community admins for listing cards ("Chaired by").
     *
     * @param  array<int, CommunityOfPractice>  $items
     */
    protected function attachListingChairs(array $items, $adminByCid): void
    {
        $chairUserIds = [];

        foreach ($items as $c) {
            if (! $c instanceof CommunityOfPractice) {
                continue;
            }
            if (! empty($c->created_by)) {
                $chairUserIds[(int) $c->created_by] = true;
            }
            $admins = $adminByCid[$c->id] ?? $adminByCid[(string) $c->id] ?? collect();
            foreach ($admins as $adm) {
                if ($adm->user_id) {
                    $chairUserIds[(int) $adm->user_id] = true;
                }
            }
        }

        $userById = collect();
        if ($chairUserIds !== []) {
            $userById = User::query()
                ->whereIn('id', array_keys($chairUserIds))
                ->get(['id', 'name', 'photo', 'job_title', 'is_photo_external', 'author_id'])
                ->keyBy('id');
        }

        foreach ($items as $c) {
            if (! $c instanceof CommunityOfPractice) {
                continue;
            }

            $chairs = collect();
            $seen = [];

            $pushChair = function (int $userId, string $role) use (&$chairs, &$seen, $userById) {
                if ($userId <= 0 || isset($seen[$userId])) {
                    return;
                }
                $user = $userById->get($userId);
                if (! $user) {
                    return;
                }
                $seen[$userId] = true;
                $chairs->push([
                    'user' => $user,
                    'role' => $role,
                    'job_title' => community_user_display_job_title($user),
                ]);
            };

            if (! empty($c->created_by)) {
                $pushChair((int) $c->created_by, 'creator');
            }

            $admins = $adminByCid[$c->id] ?? $adminByCid[(string) $c->id] ?? collect();
            foreach ($admins as $adm) {
                $pushChair((int) $adm->user_id, 'admin');
            }

            $c->setAttribute('listing_chairs', $chairs);
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

        if (Schema::hasColumn('community_of_practices', 'slug') && empty($access_grp->slug)) {
            $access_grp->slug = SeoSlugger::forCommunity(
                (string) ($access_grp->community_name ?? ''),
                $access_grp->id ?: null
            );
        }
        
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

    public function findBySlug(string $slug, $withRelated = false)
    {
        $id = CommunityOfPractice::query()->where('slug', $slug)->value('id');

        if (! $id) {
            return null;
        }

        return $this->find($id, $withRelated);
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
        $message = (string) $request->input('message', '');
        $title = (string) $request->input('title', '');
        $emailOnly = $request->boolean('email_only');

        if ($emailOnly) {
            $sentUserIds = [];

            foreach ($communityIds as $communityId) {
                $community = CommunityOfPractice::with('approvedMembers.user')->find($communityId);

                if (!$community) {
                    continue;
                }

                $members = $community->approvedMembers;

                foreach ($members as $member) {
                    if (!empty($memberIds) && !in_array((int) $member->user_id, array_map('intval', $memberIds), true)) {
                        continue;
                    }

                    if (isset($sentUserIds[$member->user_id])) {
                        continue;
                    }

                    $user = $member->user;
                    if (!$user || empty($user->email) || !filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                        continue;
                    }

                    $sentUserIds[$member->user_id] = true;

                    $displayName = $user->name ?? 'Member';
                    $personalSubject = str_replace('{name}', $displayName, $title);
                    $personalBody = str_replace(
                        '{name}',
                        htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8'),
                        $message
                    );

                    $payload = (object) [
                        'email' => $user->email,
                        'subject' => $personalSubject,
                        'body' => $personalBody,
                    ];

                    $result = send_email($payload);
                    $ok = is_array($result)
                        ? !empty($result['success'])
                        : (is_object($result) && !empty($result->success));
                    if (!$ok) {
                        $err = is_array($result)
                            ? ($result['message'] ?? 'unknown')
                            : (is_object($result) ? ($result->message ?? 'unknown') : 'unknown');
                        \Log::warning('Messaging email_only: send failed', [
                            'user_id' => $member->user_id,
                            'email' => $user->email,
                            'message' => $err,
                        ]);
                    }
                }
            }

            return true;
        }

        // Push notification: plain text only (strip HTML if present)
        $plainTitle = str_replace('{name}', 'Member', strip_tags(html_entity_decode($title, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        $plainMessage = str_replace(
            '{name}',
            'Member',
            strip_tags(html_entity_decode($message, ENT_QUOTES | ENT_HTML5, 'UTF-8'))
        );
        $plainMessage = preg_replace("/\r\n|\r|\n/", ' ', $plainMessage);
        $plainMessage = trim(preg_replace('/\s+/', ' ', $plainMessage));

        foreach ($communityIds as $communityId) {
            $community = CommunityOfPractice::with('approvedMembers.user')->find($communityId);

            if (!$community) {
                continue;
            }

            $members = $community->approvedMembers;
            $fcmTokens = [];

            if (empty($memberIds)) {
                foreach ($members as $member) {
                    if ($member->user->fcm_token) {
                        $fcmTokens[] = $member->user->fcm_token;
                    }
                }
            } else {
                foreach ($members as $member) {
                    if (in_array((int) $member->user_id, array_map('intval', $memberIds), true) && $member->user->fcm_token) {
                        $fcmTokens[] = $member->user->fcm_token;
                    }
                }
            }

            if (!empty($fcmTokens)) {
                sendPushNotification($plainTitle, $plainMessage, $fcmTokens);
            }
        }

        return true;
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

    /**
     * Paginated approved members with publication counts in this community (same logic as web members-data).
     *
     * @return array{items: list<array<string, mixed>>, page: int, per_page: int, total: int, has_more: bool, is_community_admin: bool}
     */
    public function approvedMembersPaginatedWithStats(int $communityId, int $page, int $perPage, string $search, bool $isCommunityAdmin): array
    {
        $page = max($page, 1);
        $perPage = max($perPage, 1);

        $base = DB::table('community_of_practice_members as m')
            ->join('users as u', 'u.id', '=', 'm.user_id')
            ->leftJoin('publication as p', 'p.user_id', '=', 'u.id')
            ->leftJoin('publication_community_of_practices as pcp', function ($join) {
                $join->on('pcp.publication_id', '=', 'p.id');
                $join->on('pcp.community_of_practice_id', '=', 'm.community_of_practice_id');
            })
            ->where('m.community_of_practice_id', $communityId)
            ->where('m.is_approved', 1)
            ->groupBy('m.id', 'm.user_id', 'm.is_active', 'm.is_admin', 'u.name', 'u.email', 'u.job_title', 'u.photo', 'u.author_id', 'u.is_photo_external')
            ->select(
                'm.id as membership_id',
                'm.user_id',
                'm.is_active',
                'm.is_admin',
                'u.name',
                'u.email',
                'u.job_title',
                'u.photo',
                'u.author_id',
                'u.is_photo_external',
                DB::raw('COUNT(DISTINCT pcp.publication_id) as publication_count')
            );

        if ($search !== '') {
            $base->where(function ($q) use ($search) {
                $q->where('u.name', 'like', '%'.$search.'%')
                    ->orWhere('u.email', 'like', '%'.$search.'%')
                    ->orWhere('u.job_title', 'like', '%'.$search.'%');
            });
        }

        $recordsFiltered = DB::table(DB::raw('('.$base->toSql().') as x'))
            ->mergeBindings($base)
            ->count();

        $rows = $base
            ->orderBy('publication_count', 'desc')
            ->orderBy('name', 'asc')
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        $rankStart = (($page - 1) * $perPage) + 1;
        $items = [];
        foreach ($rows as $index => $row) {
            $userForPhoto = new User([
                'photo' => $row->photo,
                'author_id' => $row->author_id,
                'is_photo_external' => $row->is_photo_external,
                'name' => $row->name,
            ]);
            $profileUrl = ! empty($row->author_id)
                ? author_publications_url((int) $row->author_id)
                : null;

            $items[] = [
                'rank' => $rankStart + $index,
                'membership_id' => (int) $row->membership_id,
                'user_id' => (int) $row->user_id,
                'name' => (string) $row->name,
                'job_title' => (string) ($row->job_title ?: 'Not specified'),
                'email' => (string) $row->email,
                'publication_count' => (int) $row->publication_count,
                'is_admin' => (bool) $row->is_admin,
                'is_active' => (bool) $row->is_active,
                'profile_url' => $profileUrl,
                'photo_url' => community_user_has_profile_image($userForPhoto) ? (string) $row->photo : null,
                'initials' => community_user_initials((string) $row->name),
            ];
        }

        $loadedCount = (($page - 1) * $perPage) + count($items);
        $hasMore = $loadedCount < $recordsFiltered;

        return [
            'items' => $items,
            'page' => $page,
            'per_page' => $perPage,
            'total' => $recordsFiltered,
            'has_more' => $hasMore,
            'is_community_admin' => $isCommunityAdmin,
        ];
    }

    public function paginatedPublicationsForCommunity(int $communityId, int $perPage): LengthAwarePaginator
    {
        $perPage = max(1, min($perPage, 50));
        $ids = PublicationCommunityOfPractice::where('community_of_practice_id', $communityId)->pluck('publication_id');

        return Publication::whereIn('id', $ids)
            ->with(['author'])
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function paginatedForumsForCommunity(int $communityId, int $perPage): LengthAwarePaginator
    {
        $perPage = max(1, min($perPage, 50));
        $ids = ForumCommunityOfPractice::where('community_of_practice_id', $communityId)->pluck('forum_id');

        return Forum::whereIn('id', $ids)
            ->with(['user'])
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Event>
     */
    public function activeEventsForCommunity(int $communityId, int $limit = 50)
    {
        $limit = max(1, min($limit, 100));

        return Event::where('community_of_practice_id', $communityId)
            ->where('status', '!=', 'cancelled')
            ->orderBy('startdate', 'asc')
            ->limit($limit)
            ->get();
    }

    public function buildAdminCommunitiesQuery(Request $request)
    {
        $query = CommunityOfPractice::query();

        if ($request->filled('term')) {
            $term = trim((string) $request->input('term'));
            $query->where(function ($q) use ($term) {
                $q->where('community_name', 'like', '%'.$term.'%')
                    ->orWhere('description', 'like', '%'.$term.'%')
                    ->orWhereHas('creator', function ($q2) use ($term) {
                        $q2->where('name', 'like', '%'.$term.'%')
                            ->orWhere('email', 'like', '%'.$term.'%');
                    });
            });
        }

        return $query->orderBy('community_name');
    }

    public function adminCommunitiesDatatable(Request $request): array
    {
        $draw = (int) $request->input('draw', 1);
        $start = max(0, (int) $request->input('start', 0));
        $length = min(max(1, (int) $request->input('length', 15)), 100);

        $base = $this->buildAdminCommunitiesQuery($request);
        $recordsTotal = CommunityOfPractice::query()->count();
        $recordsFiltered = (clone $base)->count();

        $orderColIndex = (int) $request->input('order.0.column', 1);
        $orderDir = strtolower((string) $request->input('order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $orderMap = [0 => 'id', 1 => 'community_name', 2 => 'description'];
        $orderCol = $orderMap[$orderColIndex] ?? 'community_name';
        $base->orderBy($orderCol, $orderDir);

        $rows = $base->skip($start)->take($length)->get();
        $pendingCounts = CommunityOfPracticeMembers::query()
            ->select('community_of_practice_id', DB::raw('COUNT(*) as total'))
            ->where('is_approved', 0)
            ->whereIn('community_of_practice_id', $rows->pluck('id'))
            ->groupBy('community_of_practice_id')
            ->pluck('total', 'community_of_practice_id');

        $canDelete = auth()->user() && auth()->user()->can('delete_publication_metadata');
        $data = [];
        $index = $start + 1;

        foreach ($rows as $community) {
            $pending = (int) ($pendingCounts[$community->id] ?? 0);
            $description = e(\Illuminate\Support\Str::words(strip_tags((string) $community->description), 20, '...'));

            $actions = '<div class="btn-group-vertical btn-group-sm d-inline-flex" style="gap:4px;">'
                .'<a href="'.route('admin.commsofpractice.details', $community->id).'" class="btn btn-outline-info btn-sm" style="position:relative;">'
                .'<i class="fa fa-users mr-1"></i>Group Members';
            if ($pending > 0) {
                $actions .= '<span class="badge badge-danger badge-pill" style="position:absolute;top:-4px;right:-6px;min-width:18px;background:#dc3545!important;color:#fff!important;">'.$pending.'</span>';
            }
            $actions .= '</a>'
                .'<button type="button" class="btn btn-outline-dark btn-sm" onclick="openEditCommunity('.$community->id.')"><i class="fa fa-edit mr-1"></i>Edit</button>';
            if ($canDelete) {
                $actions .= '<button type="button" class="btn btn-outline-danger btn-sm" onclick="openDeleteModal('.$community->id.')"><i class="fa fa-trash mr-1"></i>Delete</button>';
            }
            $actions .= '</div>';

            $data[] = [
                'index' => '<span class="text-muted">'.$index++.'</span>',
                'community_name' => '<div class="pub-cell-wrap"><strong>'.e($community->community_name).'</strong></div>',
                'description' => '<div class="pub-cell-wrap">'.$description.'</div>',
                'actions' => $actions,
            ];
        }

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    /**
     * @return array{usesAdministrativeUnits: bool, geoTable: string, geoForeignKey: string, geoLabel: string, geoFilterId: int}
     */
    public function participantsGeoContext(): array
    {
        $usesAdministrativeUnits = function_exists('admin_units_enabled')
            ? (bool) admin_units_enabled()
            : (bool) env('ADMIN_UNITS_ENABLED', false);

        return [
            'usesAdministrativeUnits' => $usesAdministrativeUnits,
            'geoTable' => $usesAdministrativeUnits ? 'administrative_units' : 'country',
            'geoForeignKey' => $usesAdministrativeUnits ? 'users.administrative_unit_id' : 'users.country_id',
            'geoLabel' => $usesAdministrativeUnits ? 'Administrative Unit' : 'Country',
            'geoFilterId' => 0,
        ];
    }

    public function participantsDashboardStats(array $geo): array
    {
        $geoTable = $geo['geoTable'];
        $geoForeignKey = $geo['geoForeignKey'];

        $approvedMemberships = CommunityOfPracticeMembers::query()
            ->where('is_active', 1)
            ->where('is_approved', 1)
            ->count();

        $pendingMemberships = CommunityOfPracticeMembers::query()
            ->where('is_active', 1)
            ->where('is_approved', 0)
            ->count();

        $uniqueParticipants = CommunityOfPracticeMembers::query()
            ->where('is_active', 1)
            ->where('is_approved', 1)
            ->distinct('user_id')
            ->count('user_id');

        $totalCommunities = CommunityOfPractice::query()->count();
        $activeCommunities = CommunityOfPractice::query()->where('is_active', 1)->count();

        $participantsByGeography = CommunityOfPracticeMembers::query()
            ->from('community_of_practice_members as copm')
            ->join('users', 'users.id', '=', 'copm.user_id')
            ->leftJoin($geoTable, $geoTable.'.id', '=', $geoForeignKey)
            ->where('copm.is_active', 1)
            ->where('copm.is_approved', 1)
            ->select(
                DB::raw('COALESCE('.$geoTable.'.name, \'Unspecified\') as geography_name'),
                DB::raw('COUNT(DISTINCT users.id) as total')
            )
            ->groupBy(DB::raw('COALESCE('.$geoTable.'.name, \'Unspecified\')'))
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        return [
            'total_communities' => $totalCommunities,
            'active_communities' => $activeCommunities,
            'approved_memberships' => $approvedMemberships,
            'pending_memberships' => $pendingMemberships,
            'unique_participants' => $uniqueParticipants,
            'participants_by_geography' => $participantsByGeography,
        ];
    }

    public function applyAdminParticipantsFilters($query, Request $request, array $geo, string $userAlias = 'users'): void
    {
        $geoTable = $geo['geoTable'];
        $geoForeignKey = $geo['geoForeignKey'];
        $geoFilterId = (int) ($request->input('geography_id') ?: $request->input('country_id'));

        $query->when($request->filled('q'), function ($q) use ($request, $geoTable, $userAlias) {
            $term = trim((string) $request->q);
            $q->where(function ($qq) use ($term, $geoTable, $userAlias) {
                $qq->where($userAlias.'.name', 'like', '%'.$term.'%')
                    ->orWhere($userAlias.'.email', 'like', '%'.$term.'%')
                    ->orWhere($userAlias.'.phone_number', 'like', '%'.$term.'%')
                    ->orWhere($userAlias.'.job_title', 'like', '%'.$term.'%')
                    ->orWhere($userAlias.'.organization_name', 'like', '%'.$term.'%')
                    ->orWhere($geoTable.'.name', 'like', '%'.$term.'%')
                    ->orWhereExists(function ($sub) use ($term, $userAlias) {
                        $sub->from('community_of_practice_members as cm')
                            ->join('community_of_practices as cp', 'cp.id', '=', 'cm.community_of_practice_id')
                            ->whereColumn('cm.user_id', $userAlias.'.id')
                            ->where('cm.is_active', 1)
                            ->whereIn('cm.is_approved', [0, 1])
                            ->where('cp.community_name', 'like', '%'.$term.'%');
                    });
            });
        })
            ->when($geoFilterId > 0, function ($q) use ($geoForeignKey, $geoFilterId) {
                $q->where($geoForeignKey, $geoFilterId);
            })
            ->when($request->filled('title'), function ($q) use ($request, $userAlias) {
                $q->where($userAlias.'.job_title', 'like', '%'.trim((string) $request->title).'%');
            })
            ->when($request->filled('organisation'), function ($q) use ($request, $userAlias) {
                $q->where($userAlias.'.organization_name', 'like', '%'.trim((string) $request->organisation).'%');
            })
            ->when($request->filled('badge_type_id'), function ($q) use ($request, $userAlias) {
                $badgeTypeId = (int) $request->badge_type_id;
                $q->whereExists(function ($sub) use ($badgeTypeId, $userAlias) {
                    $sub->select(DB::raw(1))
                        ->from('user_badges as ub')
                        ->whereColumn('ub.user_id', $userAlias.'.id')
                        ->where('ub.badge_type_id', $badgeTypeId);
                });
            });
    }

    public function buildAdminApprovedParticipantsQuery(Request $request, array $geo): \Illuminate\Database\Eloquent\Builder
    {
        $geoTable = $geo['geoTable'];
        $geoForeignKey = $geo['geoForeignKey'];

        $query = User::query()
            ->select('users.*', DB::raw($geoTable.'.name as geo_name'))
            ->join('community_of_practice_members as copm', function ($join) {
                $join->on('copm.user_id', '=', 'users.id')
                    ->where('copm.is_approved', 1)
                    ->where('copm.is_active', 1);
            })
            ->leftJoin($geoTable, $geoTable.'.id', '=', $geoForeignKey);

        $this->applyAdminParticipantsFilters($query, $request, $geo);

        if ($request->filled('community_id')) {
            $communityId = (int) $request->community_id;
            $query->whereExists(function ($sub) use ($communityId) {
                $sub->from('community_of_practice_members as cx')
                    ->whereColumn('cx.user_id', 'users.id')
                    ->where('cx.community_of_practice_id', $communityId)
                    ->where('cx.is_approved', 1)
                    ->where('cx.is_active', 1);
            });
        }

        return $query->groupBy('users.id', $geoTable.'.name');
    }

    public function buildAdminPendingParticipantsQuery(Request $request, array $geo): \Illuminate\Database\Eloquent\Builder
    {
        $geoTable = $geo['geoTable'];
        $geoForeignKey = $geo['geoForeignKey'];

        $query = CommunityOfPracticeMembers::query()
            ->from('community_of_practice_members as copm')
            ->select([
                'copm.id as membership_id',
                'copm.user_id',
                'copm.community_of_practice_id',
                'copm.created_at as requested_at',
                'users.name',
                'users.email',
                'users.phone_number',
                'users.job_title',
                'users.organization_name',
                'cop.community_name',
                DB::raw($geoTable.'.name as geo_name'),
            ])
            ->join('users', 'users.id', '=', 'copm.user_id')
            ->join('community_of_practices as cop', 'cop.id', '=', 'copm.community_of_practice_id')
            ->leftJoin($geoTable, $geoTable.'.id', '=', $geoForeignKey)
            ->where('copm.is_active', 1)
            ->where('copm.is_approved', 0);

        $this->applyAdminParticipantsFilters($query, $request, $geo);

        if ($request->filled('community_id')) {
            $query->where('copm.community_of_practice_id', (int) $request->community_id);
        }

        return $query;
    }

    private function formatParticipantContact(?string $email, ?string $phone): string
    {
        $email = trim((string) $email);
        $phone = trim((string) $phone);
        if ($email !== '' && $phone !== '') {
            return $email."\n".$phone;
        }

        return $email !== '' ? $email : $phone;
    }

    /**
     * @param  array<int, string>  $communities
     */
    private function formatParticipantCommunitiesCell(array $communities, string $participantName): string
    {
        $communities = array_values(array_filter(array_map(static fn ($name) => trim((string) $name), $communities)));
        if ($communities === []) {
            return '<div class="pub-cell-wrap">—</div>';
        }

        $visible = array_slice($communities, 0, 5);
        $html = '<ol class="cop-community-list mb-0">';
        foreach ($visible as $name) {
            $html .= '<li>'.e($name).'</li>';
        }
        $html .= '</ol>';

        $remaining = count($communities) - count($visible);
        if ($remaining > 0) {
            $payload = htmlspecialchars(json_encode($communities, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');
            $html .= '<button type="button" class="btn btn-link btn-sm p-0 mt-1 js-view-all-communities"'
                .' data-member-name="'.e($participantName).'"'
                .' data-communities="'.$payload.'"'
                .'>+ '.$remaining.' more</button>';
        }

        return '<div class="pub-cell-wrap cop-communities-cell">'.$html.'</div>';
    }

    /**
     * @param  \Illuminate\Support\Collection<int, User>  $participants
     */
    public function enrichParticipantsCollection($participants): void
    {
        $userIds = $participants->pluck('id')->unique()->filter()->map(fn ($id) => (int) $id)->all();
        if ($userIds === []) {
            return;
        }

        $authorByUser = User::query()->whereIn('id', $userIds)->pluck('author_id', 'id')->filter()->map(fn ($id) => (int) $id)->toArray();

        $publicationsByUser = DB::table('publication')
            ->select('user_id', DB::raw('COUNT(*) as total'))
            ->whereIn('user_id', $userIds)
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        $publicationsByAuthor = empty($authorByUser)
            ? collect()
            : DB::table('publication')
                ->select('author_id', DB::raw('COUNT(*) as total'))
                ->whereIn('author_id', array_values($authorByUser))
                ->groupBy('author_id')
                ->pluck('total', 'author_id');

        $forumPostsByUser = DB::table('forums')
            ->select('created_by', DB::raw('COUNT(*) as total'))
            ->whereIn('created_by', $userIds)
            ->groupBy('created_by')
            ->pluck('total', 'created_by');

        $forumCommentsByUser = DB::table('forum_comments')
            ->select('created_by', DB::raw('COUNT(*) as total'))
            ->whereIn('created_by', $userIds)
            ->groupBy('created_by')
            ->pluck('total', 'created_by');

        $badgesByUser = \App\Models\UserBadge::query()
            ->with('badgeType:id,name')
            ->whereIn('user_id', $userIds)
            ->orderByDesc('awarded_at')
            ->get()
            ->groupBy('user_id');

        $communitiesByUser = DB::table('community_of_practice_members as m')
            ->join('community_of_practices as c', 'c.id', '=', 'm.community_of_practice_id')
            ->whereIn('m.user_id', $userIds)
            ->where('m.is_approved', 1)
            ->where('m.is_active', 1)
            ->orderBy('c.community_name')
            ->select('m.user_id', 'c.community_name')
            ->get()
            ->groupBy('user_id')
            ->map(function ($rows) {
                return $rows->pluck('community_name')->unique()->filter()->values()->all();
            });

        $participants->transform(function ($row) use (
            $publicationsByUser,
            $publicationsByAuthor,
            $authorByUser,
            $forumPostsByUser,
            $forumCommentsByUser,
            $badgesByUser,
            $communitiesByUser
        ) {
            $uid = (int) $row->id;
            $authorId = $authorByUser[$uid] ?? null;
            $pubByUser = (int) ($publicationsByUser[$uid] ?? 0);
            $pubByAuthor = $authorId ? (int) ($publicationsByAuthor[$authorId] ?? 0) : 0;

            $row->publication_contributions = $pubByUser + $pubByAuthor;
            $row->forum_contributions = (int) ($forumPostsByUser[$uid] ?? 0) + (int) ($forumCommentsByUser[$uid] ?? 0);

            $row->badge_labels = collect($badgesByUser[$uid] ?? [])
                ->map(fn ($b) => $b->badgeType->name ?? null)
                ->filter()
                ->unique()
                ->implode(', ');

            $row->community_names = $communitiesByUser[$uid] ?? [];

            return $row;
        });
    }

    public function adminParticipantsDatatable(Request $request): array
    {
        $geo = $this->participantsGeoContext();
        $geo['geoFilterId'] = (int) ($request->input('geography_id') ?: $request->input('country_id'));

        $draw = (int) $request->input('draw', 1);
        $start = max(0, (int) $request->input('start', 0));
        $length = min(max(1, (int) $request->input('length', 20)), 100);

        $base = $this->buildAdminApprovedParticipantsQuery($request, $geo);
        $countQuery = clone $base;
        $recordsTotal = (int) DB::table(DB::raw('('.$countQuery->toSql().') as approved_participant_rows'))
            ->mergeBindings($countQuery->getQuery())
            ->count();
        $recordsFiltered = $recordsTotal;

        $orderColIndex = (int) $request->input('order.0.column', 1);
        $orderDir = strtolower((string) $request->input('order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $orderMap = [
            1 => 'users.name',
            2 => 'users.email',
            3 => 'users.job_title',
            4 => 'users.organization_name',
            5 => 'geo_name',
        ];
        if (isset($orderMap[$orderColIndex])) {
            $base->orderBy($orderMap[$orderColIndex], $orderDir);
        } else {
            $base->orderBy('users.name', 'asc');
        }

        $rows = $base->skip($start)->take($length)->get();
        $this->enrichParticipantsCollection($rows);

        $cell = static fn (?string $value) => '<div class="pub-cell-wrap">'.e($value ?: '—').'</div>';
        $contactCell = function (?string $email, ?string $phone) {
            $formatted = $this->formatParticipantContact($email, $phone);
            if ($formatted === '') {
                return '<div class="pub-cell-wrap">—</div>';
            }
            $lines = array_map('trim', explode("\n", $formatted));

            return '<div class="pub-cell-wrap">'.implode('<br>', array_map(static fn ($line) => e($line), $lines)).'</div>';
        };
        $data = [];
        $index = $start + 1;

        foreach ($rows as $participant) {
            $data[] = [
                'index' => '<span class="text-muted">'.$index++.'</span>',
                'name' => $cell($participant->name),
                'contact' => $contactCell($participant->email ?? null, $participant->phone_number ?? null),
                'title' => $cell($participant->job_title),
                'organisation' => $cell($participant->organization_name),
                'geography' => $cell($participant->geo_name),
                'publications' => (int) ($participant->publication_contributions ?? 0),
                'forums' => (int) ($participant->forum_contributions ?? 0),
                'badges' => $cell($participant->badge_labels ?? null),
                'communities' => $this->formatParticipantCommunitiesCell(
                    is_array($participant->community_names ?? null) ? $participant->community_names : [],
                    (string) ($participant->name ?? '')
                ),
            ];
        }

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    public function adminPendingParticipantsDatatable(Request $request): array
    {
        $geo = $this->participantsGeoContext();
        $draw = (int) $request->input('draw', 1);
        $start = max(0, (int) $request->input('start', 0));
        $length = min(max(1, (int) $request->input('length', 10)), 50);

        $base = $this->buildAdminPendingParticipantsQuery($request, $geo);
        $recordsTotal = (clone $base)->count();
        $recordsFiltered = $recordsTotal;

        $rows = $base
            ->orderByDesc('copm.created_at')
            ->skip($start)
            ->take($length)
            ->get();

        $cell = static fn (?string $value) => '<div class="pub-cell-wrap">'.e($value ?: '—').'</div>';
        $contactCell = function (?string $email, ?string $phone) {
            $formatted = $this->formatParticipantContact($email, $phone);
            if ($formatted === '') {
                return '<div class="pub-cell-wrap">—</div>';
            }
            $lines = array_map('trim', explode("\n", $formatted));

            return '<div class="pub-cell-wrap">'.implode('<br>', array_map(static fn ($line) => e($line), $lines)).'</div>';
        };

        $data = [];
        $index = $start + 1;
        foreach ($rows as $row) {
            $membershipId = (int) $row->membership_id;
            $communityId = (int) $row->community_of_practice_id;
            $requested = $row->requested_at ? Carbon::parse($row->requested_at)->format('M j, Y') : '—';
            $actions = '<div class="d-flex flex-wrap gap-1 justify-content-center">'
                .'<button type="button" class="btn btn-success btn-sm js-pending-approve" data-member-id="'.$membershipId.'" data-community-id="'.$communityId.'" title="Approve"><i class="fa fa-check"></i></button>'
                .'<button type="button" class="btn btn-outline-danger btn-sm js-pending-reject" data-member-id="'.$membershipId.'" data-community-id="'.$communityId.'" title="Reject"><i class="fa fa-times"></i></button>'
                .'<a href="'.e(route('admin.commsofpractice.details', $communityId)).'" class="btn btn-outline-secondary btn-sm" title="View community"><i class="fa fa-external-link-alt"></i></a>'
                .'</div>';

            $data[] = [
                'select' => '<span class="cop-pending-checkbox-wrap"><input type="checkbox" class="cop-pending-checkbox js-pending-select" value="'.$membershipId.'" data-community-id="'.$communityId.'" aria-label="Select request"></span>',
                'index' => '<span class="text-muted">'.$index++.'</span>',
                'name' => $cell($row->name),
                'contact' => $contactCell($row->email ?? null, $row->phone_number ?? null),
                'community' => $cell($row->community_name ?? null),
                'geography' => $cell($row->geo_name ?? null),
                'requested' => $cell($requested),
                'actions' => $actions,
            ];
        }

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    public function assertUserIsCommunityMember(int $communityId, int $userId): bool
    {
        return CommunityOfPracticeMembers::where('community_of_practice_id', $communityId)
            ->where('user_id', $userId)
            ->where('is_approved', 1)
            ->where('is_active', 1)
            ->exists();
    }

    public function saveCommunityComment(Request $request)
    {
        $communityId = (int) $request->input('id');
        $userId = (int) current_user()->id;

        if (! $this->assertUserIsCommunityMember($communityId, $userId)) {
            throw new \Illuminate\Auth\Access\AuthorizationException('Only active community members can comment.');
        }

        $comment = new CommunityComment();
        $comment->created_by = $userId;
        $comment->community_of_practice_id = $communityId;
        $comment->comment = sanitize_rich_text_for_storage(clean_unicode($request->comment ?? ''));
        $comment->parent_id = $request->parent_id ?? null;

        $autoApprove = settings()->auto_approve_comments ?? true;
        $comment->status = $autoApprove ? 'approved' : 'pending';

        $comment->save();

        if ($request->hasFile('attachments') && $comment->id) {
            $files = $request->file('attachments');

            try {
                $this->saveCommunityCommentAttachments($files, $comment->id);
            } catch (\Exception $e) {
                \Log::error('Failed to save community comment attachments', [
                    'comment_id' => $comment->id,
                    'community_id' => $communityId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($comment->id) {
            $comment->refresh();
            $comment->attachments;
        }

        return $comment;
    }

    public function toggleCommunityCommentLike($commentId, ?int $userId = null)
    {
        $userId = $userId ?? auth()->id();
        $comment = CommunityComment::find($commentId);

        if (! $comment || ! $userId) {
            return [
                'liked' => false,
                'count' => 0,
            ];
        }

        if (! $this->assertUserIsCommunityMember((int) $comment->community_of_practice_id, (int) $userId)) {
            throw new \Illuminate\Auth\Access\AuthorizationException('Only active community members can like comments.');
        }

        $like = CommunityCommentLike::where('community_comment_id', $commentId)
            ->where('user_id', $userId)
            ->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            CommunityCommentLike::create([
                'community_comment_id' => $commentId,
                'user_id' => $userId,
            ]);
            $liked = true;
        }

        $count = CommunityCommentLike::where('community_comment_id', $commentId)->count();

        return [
            'liked' => $liked,
            'count' => $count,
        ];
    }

    /**
     * @param  array|\Illuminate\Http\UploadedFile  $files
     */
    private function saveCommunityCommentAttachments($files, $comment_id): int
    {
        if (! $comment_id || ! CommunityComment::find($comment_id)) {
            \Log::error('Invalid community comment ID provided for attachment save', [
                'comment_id' => $comment_id,
            ]);

            return 0;
        }

        $officeService = app(OfficeDocumentToPdfService::class);
        $allowedExtensions = array_values(array_unique(array_merge([
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf',
            'mp4', 'm4v', 'mov', 'avi', 'webm', 'mkv', 'wmv', 'flv', '3gp', '3gpp', 'mpeg', 'mpg',
            'mp3', 'm4a', 'wav', 'aac', 'ogg', 'oga', 'opus', 'flac', 'wma',
        ], OfficeDocumentToPdfService::CONVERTIBLE_EXTENSIONS)));
        $rasterPdfMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];
        $octetStreamExtensions = array_values(array_unique(array_merge([
            'mp4', 'm4v', 'mov', 'avi', 'webm', 'mkv', 'wmv', 'flv', '3gp', '3gpp', 'mpeg', 'mpg',
            'mp3', 'm4a', 'wav', 'aac', 'ogg', 'oga', 'opus', 'flac', 'wma',
        ], OfficeDocumentToPdfService::CONVERTIBLE_EXTENSIONS)));
        $maxFileSize = 2 * 1024 * 1024;
        $dangerousExtensions = ['exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js', 'jar', 'apk', 'dll', 'sh', 'php', 'asp', 'jsp', 'py', 'rb', 'pl', 'cgi', 'bin', 'msi', 'deb', 'rpm'];

        $upfiles = (! is_array($files)) ? [$files] : $files;
        $savedCount = 0;

        foreach ($upfiles as $file) {
            if (! $file || ! $file->isValid()) {
                continue;
            }

            $extension = strtolower($file->guessExtension() ?: pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
            $fileSize = $file->getSize();

            if (! in_array($extension, $allowedExtensions)) {
                continue;
            }

            $mimeNorm = strtolower((string) $file->getMimeType());
            if (in_array($mimeNorm, ['image/jpg', 'image/pjpeg'], true)) {
                $mimeNorm = 'image/jpeg';
            }

            $isOfficeConvertible = $officeService->isConvertibleExtension($extension);

            $mimeAllowed = in_array($mimeNorm, $rasterPdfMimes, true)
                || Str::startsWith($mimeNorm, 'video/')
                || Str::startsWith($mimeNorm, 'audio/')
                || ($mimeNorm === 'application/octet-stream' && in_array($extension, $octetStreamExtensions, true))
                || $isOfficeConvertible;

            if (Str::startsWith($mimeNorm, 'image/') && ! in_array($mimeNorm, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], true) && ! $isOfficeConvertible) {
                $mimeAllowed = false;
            }

            if (! $mimeAllowed || $fileSize > $maxFileSize || in_array($extension, $dangerousExtensions)) {
                continue;
            }

            try {
                $original_filename = str_replace(["\0", "\r"], '', (string) $file->getClientOriginalName());
                $file_name = md5_file($file->getRealPath());
                $file_path = 'community/'.$file_name.'.'.$extension;

                $storagePath = hub_storage_path('uploads/community').'/';
                if (! is_dir($storagePath)) {
                    mkdir($storagePath, 0755, true);
                }

                $moved = $file->move($storagePath, $file_name.'.'.$extension);
                if (! $moved) {
                    throw new \Exception('Failed to move uploaded file to '.$storagePath);
                }

                $finalPath = $storagePath.$file_name.'.'.$extension;
                if (! file_exists($finalPath)) {
                    throw new \Exception('File does not exist after move: '.$finalPath);
                }

                if ($officeService->isConvertibleExtension($extension)) {
                    $pdfPath = $officeService->convertToPdf($finalPath);
                    if ($pdfPath && is_file($pdfPath) && filesize($pdfPath) > 0) {
                        if (is_file($finalPath) && $finalPath !== $pdfPath) {
                            @unlink($finalPath);
                        }
                        $extension = 'pdf';
                        $file_path = 'community/'.$file_name.'.pdf';
                        $original_filename = pathinfo($original_filename, PATHINFO_FILENAME).'.pdf';
                        $finalPath = $pdfPath;
                    }
                }

                CustomAttachment::create([
                    'model' => 'community_comments',
                    'path' => $file_path,
                    'name' => $original_filename,
                    'stored_filename' => basename($file_path),
                    'record_id' => $comment_id,
                ]);

                $savedCount++;
            } catch (\Exception $e) {
                \Log::error('Error saving community comment attachment: '.$e->getMessage(), [
                    'comment_id' => $comment_id,
                    'filename' => $file->getClientOriginalName(),
                ]);
            }
        }

        return $savedCount;
    }
}
