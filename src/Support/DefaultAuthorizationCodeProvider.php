<?php

namespace Wacky159\TelenorMM\Support;

use Wacky159\TelenorMM\Contracts\AuthorizationCodeProvider;
use Illuminate\Support\Facades\Cache;

class DefaultAuthorizationCodeProvider implements AuthorizationCodeProvider
{
    public function getAuthorizationCode(): ?string
    {
        return Cache::get('telenor_mm_authorization_code');
    }
}
