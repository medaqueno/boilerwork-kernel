#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Authentication\AuthInfo;

trait HasAuthInfo
{
    /**
     * Adds AuthInfo to the Isolated Container
     **/
    public function setAuthInfo(): void
    {
        container()->instance('AuthInfo', $this->authInfo());
    }
}
