<?php

namespace App\Support;

use App\Models\User;

class ApiUserDetails
{
    public static function loadForFull(int $id): ?User
    {
        return User::query()
            ->with(['preferences', 'country', 'author', 'access_level', 'communities'])
            ->find($id);
    }

    public static function loadForPublic(int $id): ?User
    {
        return User::query()
            ->with(['country', 'author'])
            ->find($id);
    }

    /**
     * Directory-safe user details (no email, phone, or secrets).
     *
     * @return array<string, mixed>
     */
    public static function publicDetails(User $user): array
    {
        $country = $user->country;
        $author = $user->author;

        return [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'names' => $user->names,
            'photo' => $user->photo,
            'job_title' => $user->job_title,
            'organization_name' => $user->organization_name,
            'orcid' => $user->orcid,
            'country_id' => $user->country_id,
            'country' => $country ? [
                'id' => $country->id,
                'name' => $country->name,
            ] : null,
            'author_id' => $user->author_id,
            'author' => $author ? [
                'id' => $author->id,
                'name' => $author->name,
            ] : null,
        ];
    }

    /**
     * Full account payload used by GET /api/profile and GET /api/users/me.
     *
     * @return array<string, mixed>
     */
    public static function fullDetails(User $user): array
    {
        $payload = $user->makeHidden(['password', 'remember_token'])->toArray();
        $payload['preference_subtheme_ids'] = $user->relationLoaded('preferences')
            ? $user->preferences->pluck('subtheme_id')->values()->all()
            : [];
        $payload['level_id'] = $user->access_level_id;
        $payload['community_ids'] = $user->relationLoaded('communities')
            ? $user->communities->pluck('id')->values()->all()
            : [];

        return $payload;
    }
}
