#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Infra\Persistence\QueryBuilder;

final class Paging
{
    public function __construct(
        private readonly int $perPage,
        private readonly int $page,
    ) {
        container()->instance('Paging', $this);
    }

    public function perPage(): int
    {
        return $this->perPage;
    }

    public function page(): int
    {
        return $this->page;
    }

    public function serialize(): array
    {
        return [
            'perPage' => $this->perPage,
            'page' => $this->page,
        ];
    }
}
