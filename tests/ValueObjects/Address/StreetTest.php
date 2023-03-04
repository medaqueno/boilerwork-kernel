<?php

declare(strict_types=1);

use App\Core\DigitalCatalogue\Domain\Model\Accommodation\ValueObjects\Address\Street;
use Boilerwork\Validation\CustomAssertionFailedException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass App\Core\DigitalCatalogue\Domain\Model\Accommodation\ValueObjects\Address\Street
 * @group accommodation
 */
class StreetTest extends TestCase
{
    /**
     * @test
     * @covers ::fromValues
     */
    public function it_should_create_street_with_valid_data(): void
    {
        $street = Street::fromValues('Gran Vía', 10, 'Puerta 1', 'Planta 1');
        $this->assertSame([
            'name' => 'Gran Vía',
            'number' => 10,
            'other1' => 'Puerta 1',
            'other2' => 'Planta 1'
        ], $street->value());
        $this->assertSame('Gran Vía', $street->name());
        $this->assertSame(10, $street->number());
        $this->assertSame('Puerta 1', $street->other1());
        $this->assertSame('Planta 1', $street->other2());
    }


    /**
     * @test
     * @covers ::__construct
     * @covers ::fromValues
     */
    public function it_should_throw_exception_when_creating_street_with_empty_name(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        Street::fromValues('');
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::fromValues
     */
    public function it_should_throw_exception_when_creating_street_with_invalid_name(): void
    {
        $this->expectException(TypeError::class);
        Street::fromValues(null);
    }
}
