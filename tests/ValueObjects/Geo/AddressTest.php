<?php

declare(strict_types=1);

use Boilerwork\Support\ValueObjects\Geo\Address\Address;
use Boilerwork\Support\ValueObjects\Geo\Coordinates;
use PHPUnit\Framework\TestCase;

class AddressTest extends TestCase
{
    public function testFromScalars(): void
    {
        $address = Address::fromScalars(
            'Main St',
            '123',
            'Apt 4B',
            'Building 1',
            'New York',
            'NY',
            '10001',
            40.712776,
            -74.005974
        );

        $this->assertSame('Main St', $address->street()->name());
        $this->assertSame('123', $address->street()->number());
        $this->assertSame('Apt 4B', $address->street()->other1());
        $this->assertSame('Building 1', $address->street()->other2());
        $this->assertSame('New York', $address->administrativeArea1()->toString());
        $this->assertSame('NY', $address->administrativeArea2()->toString());
        $this->assertSame('10001', $address->postalCode()->toString());
        $this->assertInstanceOf(Coordinates::class, $address->coordinates());
        $this->assertSame(40.712776, $address->coordinates()->latitude());
        $this->assertSame(-74.005974, $address->coordinates()->longitude());
    }

    public function testToString(): void
    {
        $address = Address::fromScalars(
            'Main St',
            '123',
            'Apt 4B',
            'Building 1',
            'New York',
            'NY',
            '10001',
            40.712776,
            -74.005974
        );

        $this->assertSame('Main St 123, Apt 4B Building 1 New York NY 10001', $address->toString());
    }

    public function testToArray(): void
    {
        $address = Address::fromScalars(
            'Main St',
            '123',
            'Apt 4B',
            'Building 1',
            'New York',
            'NY',
            '10001',
            40.712776,
            -74.005974
        );

        $expectedArray = [
            'street' => [
                'name' => 'Main St',
                'number' => '123',
                'other1' => 'Apt 4B',
                'other2' => 'Building 1'
            ],
            'administrative_area_1' => 'New York',
            'administrative_area_2' => 'NY',
            'postal_code' => '10001',
            'coordinates' => [
                'latitude' => 40.712776,
                'longitude' => -74.005974
            ],
        ];

        $this->assertSame($expectedArray, $address->toArray());
    }
}
