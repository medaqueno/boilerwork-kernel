#!/usr/bin/env php
<?php

declare(strict_types=1);

use Boilerwork\Support\ValueObjects\Address\PostalCode;
use Boilerwork\Support\ValueObjects\Country\Iso31661Alpha2Code;
use Boilerwork\Validation\CustomAssertionFailedException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Boilerwork\Support\ValueObjects\Address\PostalCode
 * @group accommodation
 */
class PostalCodeTest extends TestCase
{
    public function testCanBeCreated(): void
    {
        $postalCode = PostalCode::fromString('08003', Iso31661Alpha2Code::fromString('ES'));

        $this->assertEquals('08003', $postalCode->value());
    }

    public function testCannotBeCreatedWithInvalidData(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('Invalid PostalCode');

        PostalCode::fromString('123', Iso31661Alpha2Code::fromString('ES'));
    }
}
