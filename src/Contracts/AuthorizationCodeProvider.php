<?php

namespace NotificationChannels\TelenorMM\Contracts;

interface AuthorizationCodeProvider
{
    public function getAuthorizationCode(): ?string;
}
