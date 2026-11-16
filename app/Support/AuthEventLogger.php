<?php

namespace App\Support;

use App\Models\AuditTrail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Audit trail + burst alerting for authentication events. Writes rows
 * synchronously (no queue dependency) and never breaks the auth flow.
 */
final class AuthEventLogger
{
    /** New accounts from one IP within the window before admins are alerted. */
    public const BURST_LIMIT = 5;

    public const BURST_WINDOW_MINUTES = 10;

    public static function log(Request $request, string $action, string $outcome, array $context = []): void
    {
        try {
            $trail = new AuditTrail();
            $trail->action = $action;
            $trail->user_id = $context['user_id'] ?? optional($request->user())->id;
            $trail->description = json_encode(array_merge([
                'outcome' => $outcome,
                'ip' => $request->ip(),
                'user_agent' => mb_substr((string) $request->userAgent(), 0, 500),
            ], array_diff_key($context, ['user_id' => true])), JSON_UNESCAPED_SLASHES);
            $trail->save();
        } catch (Throwable $e) {
            Log::warning('Auth audit write failed', ['action' => $action, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Track an account registration and alert admins when one IP creates
     * more than BURST_LIMIT accounts inside the window.
     */
    public static function trackAccountCreation(Request $request, User $user): void
    {
        self::log($request, 'auth.account_created', 'success', [
            'user_id' => $user->id,
            'email' => (string) $user->email,
        ]);

        try {
            $key = 'auth_burst:'.sha1((string) $request->ip());
            $count = (int) Cache::get($key, 0) + 1;
            Cache::put($key, $count, Carbon::now()->addMinutes(self::BURST_WINDOW_MINUTES));

            if ($count === self::BURST_LIMIT + 1) {
                self::alertAdmins($request, $count);
            }
        } catch (Throwable $e) {
            Log::warning('Auth burst tracking failed', ['error' => $e->getMessage()]);
        }
    }

    private static function alertAdmins(Request $request, int $count): void
    {
        $message = sprintf(
            'Possible registration abuse: %d accounts created from IP %s within %d minutes.',
            $count,
            (string) $request->ip(),
            self::BURST_WINDOW_MINUTES
        );

        Log::critical($message);
        self::log($request, 'auth.abuse_alert', 'alert', ['count' => $count]);

        try {
            $admins = User::where('access_level_id', 4)->whereNotNull('email')->limit(20)->get();
            foreach ($admins as $admin) {
                $mail = [
                    'subject' => 'Knowledge Hub security alert: registration burst',
                    'email' => $admin->email,
                    'body' => $message.' Review the audit trail and block the IP if this is abuse.',
                ];
                \App\Jobs\SendMailJob::dispatch($mail)->onQueue('default');
            }
        } catch (Throwable $e) {
            Log::warning('Could not email admins about auth burst', ['error' => $e->getMessage()]);
        }
    }
}
