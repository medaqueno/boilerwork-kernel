#!/usr/bin/env php
<?php

declare(strict_types=1);

use Boilerwork\Support\ValueObjects\Address\PostalCode;
use Boilerwork\Support\ValueObjects\Country\Iso31661Alpha2Code;
use Boilerwork\Validation\CustomAssertionFailedException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Boilerwork\Support\ValueObjects\Address\PostalCode
 * @group Address
 */
class PostalCodeTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_created_from_string(): void
    {
        $postalCode = PostalCode::fromString('08001', Iso31661Alpha2Code::fromString('ES'));

        $this->assertSame('08001', $postalCode->toString());
    }

    /**
     * @test
     */
    public function it_can_not_be_created_with_empty_value(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('postalCode.notEmpty');

        $movida = PostalCode::fromString('', Iso31661Alpha2Code::fromString('ES'));
        var_dump($movida);
    }

    /**
     * @test
     */
    public function it_can_not_be_created_with_invalid_value(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('Invalid PostalCode');

        PostalCode::fromString('invalid', Iso31661Alpha2Code::fromString('ES'));
    }

    /**
     * @test
     */
    public function it_can_not_be_created_with_invalid_type(): void
    {
        $this->expectException(\TypeError::class);

        PostalCode::fromString([], Iso31661Alpha2Code::fromString('ES'));
    }
}
