<?php

namespace App\Support\Social;

class SocialLoginDeniedException extends \RuntimeException
{
    public function __construct(string $message, public readonly int $status = 403)
    {
        parent::__construct($message);
    }
}
