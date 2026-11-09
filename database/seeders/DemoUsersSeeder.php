<?php

namespace Database\Seeders;

use App\Models\AccessLevel;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DemoUsersSeeder extends Seeder
{
    public const DEMO_PASSWORD = 'Demo1234!';

    /**
     * Demo accounts covering every Spatie role and access level.
     * Password for all demo@ accounts: Demo1234!
     */
    public function run(): void
    {
        $password = Hash::make(self::DEMO_PASSWORD);

        $levelId = function (string $name): ?int {
            return AccessLevel::query()->where('level_name', $name)->orderBy('id')->value('id');
        };

        $overallId = $levelId('Overall');
        $rccId = $levelId('RCC');
        $countryId = $levelId('Country');
        $viewerId = $levelId('Viewer');

        $accounts = [
            [
                'email' => 'admin@demo.local',
                'first_name' => 'Demo',
                'last_name' => 'Admin',
                'role' => 'Admin',
                'access_level_id' => $overallId,
            ],
            [
                'email' => 'rccadmin@demo.local',
                'first_name' => 'Demo',
                'last_name' => 'RCCAdmin',
                'role' => 'RCC Admin',
                'access_level_id' => $rccId,
            ],
            [
                'email' => 'manager@demo.local',
                'first_name' => 'Demo',
                'last_name' => 'Manager',
                'role' => 'MS Manager',
                'access_level_id' => $countryId,
            ],
            [
                'email' => 'reviewer@demo.local',
                'first_name' => 'Demo',
                'last_name' => 'Reviewer',
                'role' => 'Reviewer',
                'access_level_id' => $countryId,
            ],
            [
                'email' => 'overall@demo.local',
                'first_name' => 'Demo',
                'last_name' => 'Overall',
                'role' => null,
                'access_level_id' => $overallId,
            ],
            [
                'email' => 'rcc@demo.local',
                'first_name' => 'Demo',
                'last_name' => 'RCC',
                'role' => null,
                'access_level_id' => $rccId,
            ],
            [
                'email' => 'country@demo.local',
                'first_name' => 'Demo',
                'last_name' => 'Country',
                'role' => null,
                'access_level_id' => $countryId,
            ],
            [
                'email' => 'viewer@demo.local',
                'first_name' => 'Demo',
                'last_name' => 'Viewer',
                'role' => null,
                'access_level_id' => $viewerId,
            ],
        ];

        foreach ($accounts as $account) {
            $name = trim($account['first_name'].' '.$account['last_name']);
            $user = User::query()->updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $name,
                    'first_name' => $account['first_name'],
                    'last_name' => $account['last_name'],
                    'password' => $password,
                    'access_level_id' => $account['access_level_id'],
                    'is_approved' => 1,
                    'is_verified' => 1,
                    'status' => 1,
                    'pwd_changed' => 1,
                    'email_verified_at' => now(),
                    'photo' => 'avatar.jpg',
                    'is_photo_external' => 0,
                    'verification_token' => null,
                ]
            );

            if ($account['role']) {
                $role = Role::query()->where('name', $account['role'])->first();
                if ($role) {
                    $user->syncRoles([$role]);
                }
            } else {
                $user->syncRoles([]);
            }
        }

        $this->ensureAdminsCanAccessAdminArea($overallId);
    }

    /**
     * Viewer access level blocks /admin even for Admin role (can_access_admin()).
     */
    protected function ensureAdminsCanAccessAdminArea(?int $overallId): void
    {
        if (! $overallId) {
            return;
        }

        $viewerIds = AccessLevel::query()
            ->where('level_name', 'Viewer')
            ->pluck('id')
            ->all();

        $admins = User::role('Admin')->get();
        foreach ($admins as $admin) {
            $needsOverall = $admin->access_level_id === null
                || in_array((int) $admin->access_level_id, array_map('intval', $viewerIds), true);

            if ($needsOverall) {
                $admin->access_level_id = $overallId;
            }

            $admin->pwd_changed = 1;
            $admin->is_approved = 1;
            $admin->is_verified = 1;
            $admin->status = 1;
            $admin->save();
        }
    }
}
