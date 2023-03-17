<?php

declare(strict_types=1);

use Boilerwork\Support\ValueObjects\Geo\Country\Iso31661Alpha2;
use Boilerwork\Validation\CustomAssertionFailedException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Boilerwork\Support\ValueObjects\Geo\Country\Iso31661Alpha2
 * @group geo
 */
class Iso31661Alpha2Test extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_created_from_string(): void
    {
        $iso31661Alpha2 = Iso31661Alpha2::fromString('ES');

        $this->assertSame('ES', $iso31661Alpha2->toString());
    }

    /**
     * @test
     */
    public function it_can_not_be_created_with_empty_value(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('iso31661Alpha2.notEmpty');

        Iso31661Alpha2::fromString('');
    }

    /**
     * @test
     */
    public function it_can_not_be_created_with_invalid_value(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('iso31661Alpha2.invalidFormat');

        Iso31661Alpha2::fromString('ZZ');
    }

    /**
     * @test
     * @psalm-suppress InvalidArgument
     */
    public function it_can_not_be_created_with_invalid_type(): void
    {
        $this->expectException(\TypeError::class);

        Iso31661Alpha2::fromString([]);
    }
}
