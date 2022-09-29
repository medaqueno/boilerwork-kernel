#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Jobs;

use Boilerwork\Authentication\AuthInfo\AuthInfo;
use Boilerwork\Authentication\AuthInfo\AuthInfoJobs;
use Boilerwork\Authentication\AuthInfo\AuthInfoNotFound;
use Boilerwork\Authentication\AuthInfo\HasAuthInfo;
use Boilerwork\Bus\CommandBus;
use Boilerwork\Support\ValueObjects\Identity;

abstract class AbstractJob
{
    use HasAuthInfo;

    protected string $jobName = __CLASS__;

    abstract public function handle(): void;

    final public function __construct()
    {
        $this->setAuthInfo();
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
