#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\Jobs;

use Boilerwork\Application\CommandBus;
use Boilerwork\Domain\ValueObjects\Identity;
use Boilerwork\System\AuthInfo\AuthInfo;
use Boilerwork\System\AuthInfo\AuthInfoJobs;
use Boilerwork\System\AuthInfo\AuthInfoNotFound;
use Boilerwork\System\AuthInfo\HasAuthInfo;

abstract class AbstractJob implements HasAuthInfo
{
    protected string $jobName = __CLASS__;

    abstract public function handle(): void;

    final public function __construct()
    {
        // $this->setAuthInfo();
    }

    /**
     * Adds AuthInfo in the Container
     **/
    public function setAuthInfo(): void
    {
        container()->instance('AuthInfo', $this->authInfo());
    }

    /**
     * Return user metadata relative.
     **/
    public function authInfo(): AuthInfo
    {
        try {
            $response =  new AuthInfoJobs(
                jobName: $this->jobName,
                transactionId: Identity::create(),
            );
        } catch (\Exception $e) {
            $response = new AuthInfoNotFound();
        }

        return $response;
    }

    final public function command(): CommandBus
    {
        return new CommandBus();
    }
}
