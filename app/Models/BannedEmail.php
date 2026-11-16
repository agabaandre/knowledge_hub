<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BannedEmail extends Model
{
    protected $fillable = ['email', 'reason', 'banned_by'];

    public static function isBanned(?string $email): bool
    {
        $email = strtolower(trim((string) $email));
        if ($email === '') {
            return false;
        }

        return static::where('email', $email)->exists();
    }
}
