#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\AuthInfo;

interface HasAuthInfo
{
    public function setAuthInfo(): void;
    public function getAuthInfo(): AuthInfo;
}
