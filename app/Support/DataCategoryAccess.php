<?php

namespace App\Support;

use App\Models\DataCategory;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

final class DataCategoryAccess
{
    public const DEFAULT_PERMISSION = 'view_workforce';

    public static function requiredPermission($category): ?string
    {
        if (! is_object($category)) {
            return null;
        }

        $named = trim((string) ($category->required_permission ?? ''));
        if ($named !== '') {
            return $named;
        }

        if (! empty($category->is_restricted)) {
            return self::DEFAULT_PERMISSION;
        }

        return null;
    }

    public static function userCanView($category, $user = null): bool
    {
        if (! is_object($category)) {
            return true;
        }

        $user = $user ?? auth()->user();
        $permission = self::requiredPermission($category);

        if ($permission !== null) {
            return $user && $user->can($permission);
        }

        if (! empty($category->is_special)) {
            return (bool) $user;
        }

        return true;
    }

    /**
     * @return list<int>
     */
    public static function inaccessibleIds($user = null): array
    {
        if (! Schema::hasTable('data_categories')) {
            return [];
        }

        $hasRestricted = Schema::hasColumn('data_categories', 'is_restricted');
        $hasPermission = Schema::hasColumn('data_categories', 'required_permission');
        if (! $hasRestricted && ! $hasPermission) {
            return [];
        }

        $user = $user ?? auth()->user();
        $query = DataCategory::query();

        $query->where(function ($q) use ($hasRestricted, $hasPermission) {
            if ($hasRestricted) {
                $q->orWhere('is_restricted', 1);
            }
            if ($hasPermission) {
                $q->orWhere(function ($inner) {
                    $inner->whereNotNull('required_permission')
                        ->where('required_permission', '!=', '');
                });
            }
        });

        return $query->get()
            ->filter(fn ($category) => ! self::userCanView($category, $user))
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
    }

    public static function assertCanView(?Model $category): void
    {
        if (! $category) {
            return;
        }

        if (self::userCanView($category)) {
            return;
        }

        if (! auth()->check()) {
            throw new AuthenticationException();
        }

        abort(403, 'You do not have permission to view this category.');
    }

    public static function assertRequestAllowed(Request $request): void
    {
        foreach (self::categoryIdsFromRequest($request) as $id) {
            self::assertCanView(DataCategory::query()->find($id));
        }
    }

    /**
     * @return list<int>
     */
    public static function categoryIdsFromRequest(Request $request): array
    {
        $raw = $request->input('data_category_id', $request->input('category'));
        if ($raw === null || $raw === '' || $raw === 'all') {
            return [];
        }
        if (! is_array($raw)) {
            $raw = [$raw];
        }

        return array_values(array_unique(array_filter(array_map('intval', $raw), fn ($id) => $id > 0)));
    }
}
