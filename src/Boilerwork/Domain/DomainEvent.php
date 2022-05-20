#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Domain;

interface DomainEvent
{
    public function getAggregateId(): string;

    public function serialize(): array;

    public static function unserialize(array $event): self;

    public function isPublic(): bool;

    public function getQueue(): ?string;

    public function getExchange(): ?string;
}
