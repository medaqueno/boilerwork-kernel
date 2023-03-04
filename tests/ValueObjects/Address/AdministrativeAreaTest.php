<?php

declare(strict_types=1);

use App\Core\DigitalCatalogue\Domain\Model\Accommodation\ValueObjects\Address\AdministrativeArea;
use Boilerwork\Validation\CustomAssertionFailedException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Core\DigitalCatalogue\Domain\Model\Accommodation\ValueObjects\Address\AdministrativeArea
 * @group accommodation
 */
class AdministrativeAreaTest extends TestCase
{
    /**
     * @test
     * @covers ::fromString
     * @covers ::value
     * @covers ::toString
     */
    public function it_should_create_administrative_area_with_valid_data(): void
    {
        $administrativeArea = AdministrativeArea::fromString('California');
        $this->assertSame('California', $administrativeArea->value());
        $this->assertSame('California', $administrativeArea->toString());
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::fromString
     */
    public function it_should_throw_exception_when_creating_administrative_area_with_invalid_value_type(): void
    {
        $this->expectException(TypeError::class);
        AdministrativeArea::fromString(null);
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::fromString
     */
    public function it_should_throw_exception_when_creating_administrative_area_with_invalid_length(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        AdministrativeArea::fromString('abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzab');
    }
}
