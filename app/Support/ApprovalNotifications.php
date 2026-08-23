<?php

namespace App\Support;

use App\Jobs\NotifyApprovers;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class ApprovalNotifications
{
    /**
     * @return list<string>
     */
    public static function permissionsFor(string $type): array
    {
        return match ($type) {
            'publication' => ['moderate_publication'],
            'forum' => ['moderate_forum'],
            'cop_participant' => ['moderate_cop_participants'],
            'federated' => ['moderate_publication', 'moderate_forum'],
            default => [],
        };
    }

    public static function typeLabel(string $type): string
    {
        return match ($type) {
            'publication' => 'publication',
            'forum' => 'forum',
            'cop_participant' => 'community membership request',
            'federated' => 'federated content',
            default => str_replace('_', ' ', $type),
        };
    }

    public static function inboxUrl(?string $type = null, ?string $search = null): string
    {
        $params = [];
        if (is_string($type) && $type !== '' && $type !== 'all') {
            $params['type'] = $type;
        }
        if (is_string($search) && trim($search) !== '') {
            $params['q'] = Str::limit(trim($search), 80, '');
        }

        try {
            return route('admin.approvals.index', $params);
        } catch (\Throwable $e) {
            $url = url('admin/approvals');
            if ($params) {
                $url .= '?'.http_build_query($params);
            }

            return $url;
        }
    }

    public static function canApprove(?User $user, string $type): bool
    {
        if ($user === null) {
            return false;
        }

        foreach (self::permissionsFor($type) as $permission) {
            try {
                if ($user->can($permission)) {
                    return true;
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        return false;
    }

    /**
     * @return Collection<int, User>
     */
    public static function recipientsFor(string $type): Collection
    {
        $users = collect();

        foreach (self::permissionsFor($type) as $permission) {
            try {
                $users = $users->merge(User::permission($permission)->get());
            } catch (\Throwable $e) {
                \Log::warning('ApprovalNotifications: could not load recipients for '.$permission.': '.$e->getMessage());
            }
        }

        return $users
            ->unique('id')
            ->filter(static fn ($user) => filled($user->email ?? null))
            ->values();
    }

    public static function notify(
        string $type,
        int $contentId,
        string $title,
        string $description = '',
        string $authorName = ''
    ): void {
        NotifyApprovers::dispatch(
            $type,
            $contentId,
            $title,
            $description,
            $authorName,
            self::inboxUrl($type, $title)
        )->onQueue('default');
    }
}
