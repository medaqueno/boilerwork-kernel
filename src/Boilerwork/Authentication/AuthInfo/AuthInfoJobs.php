#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Authentication\AuthInfo;

use Boilerwork\Support\ValueObjects\Identity;

class AuthInfoJobs extends AuthInfo
{
    public function __construct(
        public readonly string $jobName,
        public readonly Identity $transactionId,
    ) {
    }

    public function serialize(): array
    {
        return [
            'userId' => $this->jobName,
            'permissions' => [],
            'tenantId' => 'BackgroundJob',
            'transactionId' => $this->transactionId->toPrimitive(),
            'region' => null,
        ];
    }
}
