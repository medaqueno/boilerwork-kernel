#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\ValueObjects\Domain;

use Boilerwork\Foundation\ValueObjects\ValueObject;
use Boilerwork\Support\Services\FeesCalculator\ValueObjects\Money\Money;
use Boilerwork\Support\ValueObjects\Time\DateTime;
use Boilerwork\Validation\Assert;

final class CancellationPolicy extends ValueObject
{
    private function __construct(
        private readonly ?Money $money,
        protected string        $detail,
        protected ?DateTime     $dateFrom
    ) {
        Assert::lazy()->tryAll()
            ->that($detail)
            ->notEmpty('Detail must not be empty', 'detailCancellationPolicy.notEmpty')
            ->verifyNow();
    }

    public static function fromScalars(
        ?float $amount,
        ?string $iso3,
        string $detail,
        ?string $dateFrom
    ): self {
        $money = $amount && $iso3 ? Money::fromData($amount, $iso3) : null;
        $dateCp = isset($dateFrom) ? DateTime::fromString($dateFrom) : null;

        return new self(
            money: $money,
            detail: $detail,
            dateFrom: $dateCp
        );
    }

    /**
     *
     */
    public function toArray(): array
    {
        return [
            'price' => $this->money?->toArray(),
            'detail' => $this->detail,
            'dateFrom' => $this->dateFrom?->toString(),
        ];
    }

    /**
     *
     */
    public function price(): Money|null
    {
        return $this->money;
    }

    /**
     *
     */
    public function dateFrom(): DateTime|null
    {
        return $this->dateFrom;
    }

    /**
     *
     */
    public function detail(): string
    {
        return $this->detail;
    }
}