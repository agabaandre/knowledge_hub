<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\AwardCommunityBadgesJob;
use App\Models\Author;
use App\Models\BadgeType;
use App\Models\User;
use App\Models\UserLifetimeBadge;
use App\Services\ContributorBadgeAwardService;
use App\Support\QueueHealth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ParticipantBadgeManagementController extends Controller
{
    public function index(Request $request, ContributorBadgeAwardService $service)
    {
        $badgeTypes = BadgeType::query()
            ->orderBy('sort_order')
            ->orderBy('contribution_threshold')
            ->get();

        $lifetimeBadges = UserLifetimeBadge::query()
            ->with(['user.author', 'badgeType'])
            ->whereNotNull('badge_type_id')
            ->orderByDesc('lifetime_contributions')
            ->paginate(40)
            ->withQueryString();

        $authors = Author::query()
            ->withCount('publications')
            ->with(['user.lifetimeBadge.badgeType'])
            ->whereHas('publications')
            ->orderBy('name')
            ->paginate(25, ['*'], 'authors_page')
            ->withQueryString();

        $usersForAward = User::query()
            ->orderBy('name')
            ->limit(3000)
            ->get(['id', 'name', 'email']);

        $defaultPeriod = app(ContributorBadgeAwardService::class)->defaultPeriod();
        $queueHealth = QueueHealth::snapshot();
        $lastAwardRun = Cache::get('badges_last_award_run');
        $awardJobRunning = Cache::get('badges_award_job_running');
        $awardJobProgress = Cache::get('badges_award_job_progress');
        $badgeAudit = $service->auditBadgeHolders(true, 100);

        return view('admin.participant-badges.index', compact(
            'badgeTypes',
            'lifetimeBadges',
            'authors',
            'usersForAward',
            'defaultPeriod',
            'queueHealth',
            'lastAwardRun',
            'awardJobRunning',
            'awardJobProgress',
            'badgeAudit'
        ));
    }

    public function jobStatus()
    {
        return response()->json([
            'running' => (bool) Cache::get('badges_award_job_running'),
            'progress' => Cache::get('badges_award_job_progress'),
            'last_run' => Cache::get('badges_last_award_run'),
        ]);
    }

    public function audit(Request $request, ContributorBadgeAwardService $service)
    {
        $onlyMismatches = $request->boolean('only_mismatches', true);

        return response()->json(
            $service->auditBadgeHolders($onlyMismatches, (int) $request->input('limit', 200))
        );
    }

    public function runAwardJob(Request $request, ContributorBadgeAwardService $service)
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
            Cache::put('badges_award_job_running', [
                'started_at' => now()->toIso8601String(),
                'year' => $year,
                'month' => $month,
                'triggered_by' => $triggeredBy,
            ], now()->addHours(2));

            $result = $service->awardForPeriod($year, $month, $triggeredBy);
            Cache::put('badges_last_award_run', $result, now()->addDays(120));
            Cache::forget('badges_award_job_running');

            if ($result['status'] === 'failed') {
                return redirect()
                    ->route('admin.participant-badges.index')
                    ->with('error', 'Badge awarding failed: '.($result['error'] ?? 'unknown error'));
            }

            return redirect()
                ->route('admin.participant-badges.index')
                ->with('success', sprintf(
                    'Badge run completed for %s: %d user(s) processed, %d upgrade(s), %d community row(s) synced.',
                    $result['period_label'] ?? Carbon::create($year, $month, 1)->format('F Y'),
                    (int) ($result['users_processed'] ?? 0),
                    (int) ($result['badges_upgraded'] ?? 0),
                    (int) ($result['community_rows_synced'] ?? 0)
                ));
        }

        Cache::put('badges_award_job_running', [
            'started_at' => now()->toIso8601String(),
            'year' => $year,
            'month' => $month,
            'triggered_by' => $triggeredBy,
        ], now()->addHours(2));

        AwardCommunityBadgesJob::dispatch($year, $month, $triggeredBy);

        return redirect()
            ->route('admin.participant-badges.index')
            ->with('success', sprintf(
                'Badge awarding job queued for %s. Refresh this page after the queue worker processes it.',
                Carbon::create($year, $month, 1)->format('F Y')
            ));
    }

    public function award(Request $request, ContributorBadgeAwardService $service)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'badge_type_id' => 'required|integer|exists:badge_types,id',
            'lifetime_contributions' => 'nullable|integer|min:0',
        ]);

        $user = User::query()->findOrFail($validated['user_id']);
        $badgeType = BadgeType::findOrFail($validated['badge_type_id']);
        $lifetime = $validated['lifetime_contributions'] ?? max(
            (int) $badgeType->contribution_threshold,
            $service->countLifetimeContributions((int) $user->id)
        );

        UserLifetimeBadge::query()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'badge_type_id' => $badgeType->id,
                'lifetime_contributions' => $lifetime,
                'last_upgraded_at' => now(),
            ]
        );

        return redirect()
            ->route('admin.participant-badges.index')
            ->with('success', 'Lifetime contributor badge set successfully.');
    }

    public function revoke(UserLifetimeBadge $userLifetimeBadge)
    {
        $userLifetimeBadge->update([
            'badge_type_id' => null,
            'last_upgraded_at' => null,
        ]);

        return redirect()
            ->route('admin.participant-badges.index')
            ->with('success', 'Lifetime badge tier removed for this user.');
    }
}
