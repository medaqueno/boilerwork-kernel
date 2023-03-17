<?php

declare(strict_types=1);

use Boilerwork\Support\ValueObjects\Geo\Address\AdministrativeArea;
use Boilerwork\Validation\CustomAssertionFailedException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Boilerwork\Support\ValueObjects\Geo\Address\AdministrativeArea
 */
class AdministrativeAreaTest extends TestCase
{
    public function testCanCreateFromString(): void
    {
        $adminArea = AdministrativeArea::fromString('New York');
        $this->assertInstanceOf(AdministrativeArea::class, $adminArea);
        $this->assertEquals('New York', $adminArea->toString());
    }

    public function testCannotCreateFromStringWithInvalidType(): void
    {
        $this->expectException(TypeError::class);
        AdministrativeArea::fromString(123);
    }

    public function testCannotCreateFromStringWithInvalidLength(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('administrativeArea.invalidLength');
        AdministrativeArea::fromString(str_repeat('a', 129));
    }
}
