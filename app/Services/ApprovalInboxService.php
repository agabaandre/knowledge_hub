<?php

namespace App\Services;

use App\Models\CommunityOfPractice;
use App\Models\CommunityOfPracticeMembers;
use App\Models\FederatedContentItem;
use App\Models\Forum;
use App\Models\Publication;
use App\Models\User;
use App\Support\ContentModeration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class ApprovalInboxService
{
    public const TYPES = ['publication', 'forum', 'cop_participant', 'federated'];

    /**
     * @return array{all:int,publication:int,forum:int,cop_participant:int,federated:int}
     */
    public function counts(?User $user = null): array
    {
        $items = $this->pendingItems(null, $user);

        return [
            'all' => $items->count(),
            'publication' => $items->where('type', 'publication')->count(),
            'forum' => $items->where('type', 'forum')->count(),
            'cop_participant' => $items->where('type', 'cop_participant')->count(),
            'federated' => $items->where('type', 'federated')->count(),
        ];
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function pendingItems(?string $type = null, ?User $user = null): Collection
    {
        $user = $user ?? auth()->user();
        $type = ($type === 'all' || $type === '') ? null : $type;
        $items = collect();

        if ($this->canSee($user, 'publication') && ($type === null || $type === 'publication')) {
            $items = $items->concat($this->pendingPublications());
        }
        if ($this->canSee($user, 'forum') && ($type === null || $type === 'forum')) {
            $items = $items->concat($this->pendingForums());
        }
        if ($this->canSee($user, 'cop_participant') && ($type === null || $type === 'cop_participant')) {
            $items = $items->concat($this->pendingCopParticipants());
        }
        if ($this->canSee($user, 'federated') && ($type === null || $type === 'federated')) {
            $items = $items->concat($this->pendingFederated());
        }

        return $items->sortByDesc(function (array $item) {
            return optional($item['submitted_at'])->timestamp ?? 0;
        })->values();
    }

    public function canSee(?User $user, string $type): bool
    {
        if ($user === null) {
            return true;
        }

        return match ($type) {
            'publication' => ContentModeration::canModeratePublications($user) || $user->can('view_publications'),
            'forum' => ContentModeration::canModerateForums($user) || $user->can('view_forums'),
            'cop_participant' => ContentModeration::canModerateCopParticipants($user) || $user->can('view_cops'),
            'federated' => ! function_exists('federation_consumer_enabled') || federation_consumer_enabled(),
            default => false,
        };
    }

    protected function pendingPublications(): Collection
    {
        if (! Schema::hasTable('publication')) {
            return collect();
        }

        $query = Publication::query()
            ->where('is_approved', 0)
            ->where(function ($q) {
                $q->where('is_rejected', 0)->orWhereNull('is_rejected');
            })
            ->orderByDesc('id')
            ->limit(250);
        if (Schema::hasTable('users')) {
            $query->with('user');
        }

        return $query->get()->map(fn (Publication $row) => $this->item(
            'publication',
            'Publication',
            (int) $row->id,
            (string) ($row->title ?: 'Untitled resource'),
            'Public health resource',
            $this->displayName($row->user),
            $row->created_at,
            url('admin/publications/details').'?id='.$row->id
        ));
    }

    protected function pendingForums(): Collection
    {
        if (! Schema::hasTable('forums')) {
            return collect();
        }

        $query = Forum::query()
            ->pendingApproval()
            ->orderByDesc('id')
            ->limit(250);
        if (Schema::hasTable('users')) {
            $query->with('user');
        }

        return $query->get()->map(fn (Forum $row) => $this->item(
                'forum',
                'Forum',
                (int) $row->id,
                (string) ($row->forum_title ?: 'Untitled discussion'),
                'Discussion thread',
                $this->displayName($row->relationLoaded('user') ? $row->user : null),
                $row->created_at,
                url('admin/forums/details').'?id='.$row->id
            ));
    }

    protected function pendingCopParticipants(): Collection
    {
        $membersTable = (new CommunityOfPracticeMembers())->getTable();
        if (! Schema::hasTable($membersTable)) {
            return collect();
        }

        $query = CommunityOfPracticeMembers::query()
            ->where('is_approved', 0)
            ->orderByDesc('id')
            ->limit(250);
        $with = [];
        if (Schema::hasTable('users')) {
            $with[] = 'user';
        }
        if (Schema::hasTable((new CommunityOfPractice())->getTable())) {
            $with[] = 'community';
        }
        if ($with) {
            $query->with($with);
        }

        return $query->get()->map(function (CommunityOfPracticeMembers $member) {
                $person = $this->displayName($member->relationLoaded('user') ? $member->user : null);
                $communityName = $member->relationLoaded('community')
                    ? ($member->community->community_name ?? 'Community of practice')
                    : 'Community of practice';

                return $this->item(
                    'cop_participant',
                    'CoP participant',
                    (int) $member->id,
                    $person !== '—' ? $person : 'Membership request',
                    $communityName,
                    $person,
                    $member->created_at,
                    url('admin/commsofpractice/'.$member->community_of_practice_id)
                );
            });
    }

    protected function pendingFederated(): Collection
    {
        if (! Schema::hasTable('federated_content_items')) {
            return collect();
        }

        return FederatedContentItem::query()
            ->with('hub')
            ->pendingReview()
            ->orderByDesc('id')
            ->limit(250)
            ->get()
            ->map(fn (FederatedContentItem $row) => $this->item(
                'federated',
                'Federated '.ucfirst((string) ($row->content_type ?: 'content')),
                (int) $row->id,
                (string) ($row->title ?: 'Untitled federated item'),
                $row->hub->name ?? 'Partner hub',
                $row->hub->name ?? 'Partner hub',
                $row->updated_at ?? $row->created_at,
                route('admin.federation.pending-content')
            ));
    }

    protected function item(
        string $type,
        string $typeLabel,
        int $id,
        string $title,
        string $subtitle,
        string $submittedBy,
        mixed $submittedAt,
        string $previewUrl
    ): array {
        $at = $submittedAt instanceof Carbon
            ? $submittedAt
            : ($submittedAt ? Carbon::parse($submittedAt) : null);

        return [
            'type' => $type,
            'type_label' => $typeLabel,
            'id' => $id,
            'title' => $title,
            'subtitle' => $subtitle,
            'submitted_by' => $submittedBy,
            'submitted_at' => $at,
            'preview_url' => $previewUrl,
            'key' => $type.':'.$id,
        ];
    }

    protected function displayName(mixed $user): string
    {
        $name = trim((string) ($user->name ?? ''));

        return $name !== '' ? $name : '—';
    }
}
