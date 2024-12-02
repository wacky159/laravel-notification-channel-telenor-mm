<?php

namespace Wacky159\TelenorMM\Contracts;

interface AuthorizationCodeProvider
{
    public function getAuthorizationCode(): ?string;
}
