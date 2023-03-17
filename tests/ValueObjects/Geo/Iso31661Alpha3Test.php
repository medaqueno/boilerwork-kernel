<?php

declare(strict_types=1);

use Boilerwork\Support\ValueObjects\Geo\Country\Iso31661Alpha3;
use Boilerwork\Validation\CustomAssertionFailedException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Boilerwork\Support\ValueObjects\Geo\Country\Iso31661Alpha3
 * @group geo
 */
class Iso31661Alpha3Test extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_created_from_string(): void
    {
        $iso31661Alpha3 = Iso31661Alpha3::fromString('ESP');

        $this->assertSame('ESP', $iso31661Alpha3->toString());
    }

    /**
     * @test
     */
    public function it_can_not_be_created_with_empty_value(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('iso31661Alpha3.notEmpty');

        Iso31661Alpha3::fromString('');
    }

    /**
     * @test
     */
    public function it_can_not_be_created_with_invalid_value(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('iso31661Alpha3.invalidFormat');

        Iso31661Alpha3::fromString('ZZZ');
    }

    /**
     * @test
     * @psalm-suppress InvalidArgument
     */
    public function it_can_not_be_created_with_invalid_type(): void
    {
        $this->expectException(\TypeError::class);

        Iso31661Alpha3::fromString([]);
    }
}
