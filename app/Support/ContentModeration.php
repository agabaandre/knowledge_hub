<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;

final class ContentModeration
{
    public static function canModeratePublications(?User $user = null): bool
    {
        $user = $user ?? auth()->user();

        return $user !== null && $user->can('moderate_publication');
    }

    public static function canModerateForums(?User $user = null): bool
    {
        $user = $user ?? auth()->user();

        return $user !== null && $user->can('moderate_forum');
    }

    public static function canModerateCopParticipants(?User $user = null): bool
    {
        $user = $user ?? auth()->user();

        return $user !== null && $user->can('moderate_cop_participants');
    }

    public static function canModerateFederated(?User $user = null): bool
    {
        return self::canModeratePublications($user) || self::canModerateForums($user);
    }

    public static function ensureCanModeratePublications(): void
    {
        if (! self::canModeratePublications()) {
            self::deny('You do not have permission to approve or reject resources.');
        }
    }

    public static function ensureCanModerateForums(): void
    {
        if (! self::canModerateForums()) {
            self::deny('You do not have permission to approve or reject forums.');
        }
    }

    public static function ensureCanModerateCopParticipants(): void
    {
        if (! self::canModerateCopParticipants()) {
            self::deny('You do not have permission to approve or reject community participants.');
        }
    }

    public static function publicationsAutoApproveEnabled(): bool
    {
        return (bool) (settings()->auto_approve_publications ?? false);
    }

    public static function shouldAutoApprovePublication(?User $user): bool
    {
        if (! self::publicationsAutoApproveEnabled() || ! $user) {
            return false;
        }

        if (is_admin()) {
            return true;
        }

        $autoRoleIds = collect(explode(',', (string) env('AUTO_PUBLISH_ROLE_IDS', '')))
            ->filter(static fn ($value) => trim((string) $value) !== '')
            ->map(static fn ($value) => (int) trim((string) $value));

        if ($autoRoleIds->isEmpty() || ! method_exists($user, 'roles')) {
            return false;
        }

        $userRoleIds = $user->roles ? $user->roles->pluck('id') : collect();

        return $userRoleIds->intersect($autoRoleIds)->isNotEmpty();
    }

    private static function deny(string $message): void
    {
        if (request()->expectsJson() || request()->ajax()) {
            throw new HttpResponseException(response()->json([
                'status' => 'error',
                'message' => $message,
            ], 403));
        }

        abort(403, $message);
    }
}
