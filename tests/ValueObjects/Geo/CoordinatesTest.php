#!/usr/bin/env php
<?php

declare(strict_types=1);

use Boilerwork\Support\ValueObjects\Geo\Coordinates;
use PHPUnit\Framework\TestCase;
use Boilerwork\Validation\CustomAssertionFailedException;
// use Deminy\Counit\TestCase;

final class CoordinatesTest extends TestCase
{

    public function coordinatesProvider(): iterable
    {
        yield [
            new Coordinates(40.65212, -98.6568422)
        ];
    }

    /**
     * @test
     * @dataProvider coordinatesProvider
     * @covers Boilerwork\Support\ValueObjects\Geo\Coordinates
     **/
    public function testCoordinates(Coordinates $coordinates): void
    {
        $this->assertInstanceOf(
            Coordinates::class,
            $coordinates
        );
    }

    /**
     * @test
     * @covers \Boilerwork\Support\ValueObjects\Geo\Coordinates
     **/
    public function testLatitudeGt90(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('coordinates.invalidLatitude');
        new Coordinates(150.5, -10.12324);
    }

    /**
     * @test
     * @covers \Boilerwork\Support\ValueObjects\Geo\Coordinates
     **/
    public function testLatitudeLt90(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('coordinates.invalidLatitude');
        new Coordinates(-150.5, -10.12324);
    }

    /**
     * @test
     * @covers \Boilerwork\Support\ValueObjects\Geo\Coordinates
     **/
    public function testLongitudeGt180(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('coordinates.invalidLongitude');
        new Coordinates(2.1523456, 200);
    }

    /**
     * @test
     * @covers \Boilerwork\Support\ValueObjects\Geo\Coordinates
     **/
    public function testLongitudeLt180(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('coordinates.invalidLongitude');
        new Coordinates(2.1523456, -200);
    }
}
