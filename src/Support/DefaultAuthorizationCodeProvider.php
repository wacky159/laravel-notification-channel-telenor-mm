<?php

namespace NotificationChannels\TelenorMM\Support;

use NotificationChannels\TelenorMM\Contracts\AuthorizationCodeProvider;
use Illuminate\Support\Facades\Cache;

class DefaultAuthorizationCodeProvider implements AuthorizationCodeProvider
{
    public function getAuthorizationCode(): ?string
    {
        return Cache::get('telenor_mm_authorization_code');
    }
}
