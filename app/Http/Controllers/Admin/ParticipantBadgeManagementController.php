<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\AwardCommunityBadgesJob;
use App\Models\Author;
use App\Models\BadgeType;
use App\Models\CommunityOfPractice;
use App\Models\User;
use App\Models\UserBadge;
use App\Services\CommunityBadgeAwardService;
use App\Support\QueueHealth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ParticipantBadgeManagementController extends Controller
{
    public function index(Request $request)
    {
        $badgeTypes = BadgeType::query()
            ->orderBy('sort_order')
            ->orderBy('contribution_threshold')
            ->get();

        $communities = CommunityOfPractice::query()
            ->where('is_active', true)
            ->orderBy('community_name')
            ->get(['id', 'community_name']);

        $badgeAwards = UserBadge::query()
            ->with(['user.author', 'badgeType', 'community'])
            ->join('users', 'user_badges.user_id', '=', 'users.id')
            ->select('user_badges.*')
            ->selectRaw('(
                SELECT COUNT(DISTINCT p.id) FROM publication p
                WHERE p.user_id = users.id
                OR (users.author_id IS NOT NULL AND p.author_id = users.author_id)
            ) as publications_total')
            ->orderByDesc('user_badges.awarded_at')
            ->paginate(40)
            ->withQueryString();

        $authors = Author::query()
            ->withCount('publications')
            ->with(['user' => function ($q) {
                $q->with(['badges.badgeType', 'badges.community']);
            }])
            ->whereHas('publications')
            ->orderBy('name')
            ->paginate(25, ['*'], 'authors_page')
            ->withQueryString();

        $usersForAward = User::query()
            ->orderBy('name')
            ->limit(3000)
            ->get(['id', 'name', 'email']);

        $defaultPeriod = app(CommunityBadgeAwardService::class)->defaultPeriod();
        $queueHealth = QueueHealth::snapshot();
        $lastAwardRun = Cache::get('badges_last_award_run');
        $awardJobRunning = Cache::get('badges_award_job_running');

        return view('admin.participant-badges.index', compact(
            'badgeTypes',
            'communities',
            'badgeAwards',
            'authors',
            'usersForAward',
            'defaultPeriod',
            'queueHealth',
            'lastAwardRun',
            'awardJobRunning'
        ));
    }

    public function runAwardJob(Request $request, CommunityBadgeAwardService $service)
    {
        $validated = $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
            'run_mode' => 'required|in:queue,sync',
        ]);

        if (Cache::get('badges_award_job_running')) {
            return redirect()
                ->route('admin.participant-badges.index')
                ->with('error', 'A badge awarding job is already running. Please wait for it to finish.');
        }

        $year = (int) $validated['year'];
        $month = (int) $validated['month'];
        $triggeredBy = 'admin:'.(auth()->id() ?? 'unknown');

        if ($validated['run_mode'] === 'sync') {
            $result = $service->awardForPeriod($year, $month, $triggeredBy);
            Cache::put('badges_last_award_run', $result, now()->addDays(120));

            if ($result['status'] === 'failed') {
                return redirect()
                    ->route('admin.participant-badges.index')
                    ->with('error', 'Badge awarding failed: '.($result['error'] ?? 'unknown error'));
            }

            return redirect()
                ->route('admin.participant-badges.index')
                ->with('success', sprintf(
                    'Badge run completed for %s: %d badge(s) awarded, %d notification email(s) queued.',
                    $result['period_label'] ?? Carbon::create($year, $month, 1)->format('F Y'),
                    (int) $result['badges_awarded'],
                    (int) $result['emails_queued']
                ));
        }

        AwardCommunityBadgesJob::dispatch($year, $month, $triggeredBy);

        return redirect()
            ->route('admin.participant-badges.index')
            ->with('success', sprintf(
                'Badge awarding job queued for %s. Refresh this page after the queue worker processes it.',
                Carbon::create($year, $month, 1)->format('F Y')
            ));
    }

    public function award(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'community_of_practice_id' => 'required|integer|exists:community_of_practices,id',
            'badge_type_id' => 'required|integer|exists:badge_types,id',
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
            'contributions_count' => 'nullable|integer|min:0',
        ]);

        $badgeType = BadgeType::findOrFail($validated['badge_type_id']);
        $contributions = $validated['contributions_count'] ?? $badgeType->contribution_threshold;

        if (UserBadge::hasBadge(
            $validated['user_id'],
            $validated['community_of_practice_id'],
            $validated['badge_type_id'],
            $validated['year'],
            $validated['month']
        )) {
            return redirect()
                ->route('admin.participant-badges.index')
                ->with('error', 'This user already has that badge for the selected community and month.');
        }

        UserBadge::create([
            'user_id' => $validated['user_id'],
            'community_of_practice_id' => $validated['community_of_practice_id'],
            'badge_type_id' => $validated['badge_type_id'],
            'year' => $validated['year'],
            'month' => $validated['month'],
            'contributions_count' => $contributions,
            'awarded_at' => now(),
            'email_sent' => false,
        ]);

        return redirect()
            ->route('admin.participant-badges.index')
            ->with('success', 'Badge awarded successfully.');
    }

    public function revoke(UserBadge $userBadge)
    {
        $userBadge->delete();

        return redirect()
            ->route('admin.participant-badges.index')
            ->with('success', 'Badge removed.');
    }
}
