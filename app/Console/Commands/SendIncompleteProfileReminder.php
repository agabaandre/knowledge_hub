<?php

namespace App\Console\Commands;

use App\Jobs\SendMailJob;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendIncompleteProfileReminder extends Command
{
    protected $signature = 'profiles:remind-incomplete';
    protected $description = 'Send reminder emails to users with incomplete profiles';

    public function handle(): int
    {
        $usesAdministrativeUnits = function_exists('admin_units_enabled')
            ? (bool) admin_units_enabled()
            : (bool) env('ADMIN_UNITS_ENABLED', false);

        $geoField = $usesAdministrativeUnits ? 'administrative_unit_id' : 'country_id';
        $geoLabel = $usesAdministrativeUnits ? 'administrative unit' : 'country';
        $accountUrl = url('account');

        $users = User::query()
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->where(function ($q) use ($geoField) {
                $q->whereNull('job_title')
                    ->orWhere('job_title', '')
                    ->orWhereNull($geoField);
            })
            ->get(['id', 'name', 'email', 'job_title', $geoField]);

        $sent = 0;
        foreach ($users as $user) {
            try {
                $missing = [];
                if (empty(trim((string) $user->job_title))) {
                    $missing[] = 'job title';
                }
                if (empty($user->{$geoField})) {
                    $missing[] = $geoLabel;
                }

                if (empty($missing)) {
                    continue;
                }

                $body = view('emails.profile_completion_reminder', [
                    'name' => $user->name ?: 'Colleague',
                    'accountUrl' => $accountUrl,
                    'missingItems' => $missing,
                    'geoLabel' => $geoLabel,
                ])->render();

                SendMailJob::dispatch([
                    'to' => $user->email,
                    'subject' => 'Please complete your Knowledge Hub profile',
                    'title' => 'Profile completion reminder',
                    'body' => $body,
                ])->onQueue('default');
                $sent++;
            } catch (\Throwable $e) {
                Log::warning('profiles:remind-incomplete failed for user', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Profile completion reminders queued: {$sent}");

        return self::SUCCESS;
    }
}

