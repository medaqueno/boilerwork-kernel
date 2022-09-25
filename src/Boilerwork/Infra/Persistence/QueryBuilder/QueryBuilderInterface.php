#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Infra\Persistence\QueryBuilder;

interface QueryBuilderInterface
{
    public function select(array $cols = ['*']): self;
    public function from(string $table): self;

    public function update(array $cols): self;
    public function table(string $table): self;

    public function insert(array $cols = ['*']): self;
    public function into(string $table): self;

    public function delete(): self;

    public function where(string $cond): self;
    public function orWhere(string $cond): self;
    public function having(string $cond): self;
    public function orHaving(string $cond): self;
    public function groupBy(array $cols): self;
    public function join(string $joinType, string $joinToTable, string $cond): self;

    public function joinSubSelect(string $joinType, string $subSelectToJoinOn, string $asName, string $onCond): self;

    public function orderBy(array $orderBy): self;
    public function limit(int $limit): self;
    public function offset(int $limit): self;

    public function distinct(): self; // SELECT DISTINCT

    public function union(): self; // SELECT DISTINCT
    public function unionAll(): self; // SELECT DISTINCT

    public function bindValues(array $values): self;

    public function execute(): void;
    public function fetchOne(): ?array;
    public function fetchOneFromRaw(string $rawQuery, array $bindValues = []): mixed;
    public function fetchAll(): array;
    public function fetchAllFromRaw(string $rawQuery, array $bindValues = []): mixed;

    public function initTransaction(): void;
    public function endTransaction(): void;
}
