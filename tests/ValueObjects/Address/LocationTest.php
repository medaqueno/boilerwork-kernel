<?php

declare(strict_types=1);

use App\Core\DigitalCatalogue\Domain\Model\Accommodation\ValueObjects\Address\Location;
use Boilerwork\Validation\CustomAssertionFailedException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Core\DigitalCatalogue\Domain\Model\Accommodation\ValueObjects\Address\Location
 * @group accommodation
 */
class LocationTest extends TestCase
{
    /**
     * @test
     * @covers ::fromString
     * @covers ::value
     * @covers ::toString
     */
    public function it_should_create_location_with_valid_data(): void
    {
        $location = Location::fromString('San Francisco');
        $this->assertSame('San Francisco', $location->value());
        $this->assertSame('San Francisco', $location->toString());
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::fromString
     */
    public function it_should_throw_exception_when_creating_location_with_empty_value(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        Location::fromString('');
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::fromString
     */
    public function it_should_throw_exception_when_creating_location_with_invalid_value_type(): void
    {
        $this->expectException(TypeError::class);
        Location::fromString(null);
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::fromString
     */
    public function it_should_throw_exception_when_creating_location_with_invalid_length(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        Location::fromString('abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzasdadasdasasdf');
    }
}
