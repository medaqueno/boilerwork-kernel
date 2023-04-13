#!/usr/bin/env php
<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Boilerwork\Validation\CustomAssertionFailedException;
use Boilerwork\Support\ValueObjects\Domain\Flights\IATA;

// use Deminy\Counit\TestCase;

final class IATATest extends TestCase
{

    public function timeZoneProvider(): iterable
    {
        yield "ALV" => [
            IATA::fromString('ALV')
        ];

        yield "MAD" => [
            IATA::fromString('MAD')
        ];
    }

    /**
     * @test
     * @dataProvider timeZoneProvider
     * @covers \App\Core\MastersManagement\Domain\Countries\ValueObjects\Coordinates
     **/
    public function testIata(IATA $iata): void
    {
        $this->assertInstanceOf(
            IATA::class,
            $iata
        );
    }

    /**
     * @test
     * @covers \App\Core\MastersManagement\Domain\Countries\ValueObjects\Flag
     **/
    public function testInvalidIata(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('IATACode.invalidFormat');

        IATA::fromString('Europe/Barcelona');
    }

    /**
     * @test
     * @covers \App\Core\MastersManagement\Domain\Countries\ValueObjects\Flag
     **/
    public function testLengthIata(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('IATACode.invalidFormat');

        IATA::fromString('MADE');
    }
}