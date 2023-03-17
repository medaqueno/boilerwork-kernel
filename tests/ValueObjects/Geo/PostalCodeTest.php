#!/usr/bin/env php
<?php

declare(strict_types=1);

use Boilerwork\Support\ValueObjects\Geo\Address\PostalCode;
use Boilerwork\Validation\CustomAssertionFailedException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Boilerwork\Support\ValueObjects\Geo\Address\PostalCode
 */
class PostalCodeTest extends TestCase
{
    public function testFromString(): void
    {
        $value = '12345';
        $postalCode = PostalCode::fromString($value);

        $this->assertInstanceOf(PostalCode::class, $postalCode);
        $this->assertSame($value, $postalCode->toString());
    }

    public function testInvalidType(): void
    {
        $this->expectException(TypeError::class);

        PostalCode::fromString([]);
    }

    public function testNotEmpty(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('postalCode.notEmpty');

        PostalCode::fromString('');
    }

    public function testInvalidLength(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('location.invalidLength');

        $value = str_repeat('1', 25);
        PostalCode::fromString($value);
    }
}
