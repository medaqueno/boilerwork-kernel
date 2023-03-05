<?php

declare(strict_types=1);

use Boilerwork\Support\ValueObjects\Address\Address;
use Boilerwork\Support\ValueObjects\Address\AdministrativeArea;
use Boilerwork\Support\ValueObjects\Address\Location;
use Boilerwork\Support\ValueObjects\Address\PostalCode;
use Boilerwork\Support\ValueObjects\Address\Street;
use Boilerwork\Support\ValueObjects\Country\Country;
use Boilerwork\Support\ValueObjects\Country\Iso31661Alpha2Code;
use Boilerwork\Support\ValueObjects\Geo\Coordinates;
use PHPUnit\Framework\TestCase;

/**
 * @group boilerwork
 * @covers \Boilerwork\Support\ValueObjects\Address\Address
 */
class AddressTest extends TestCase
{
    public function testCanCreateFromScalars(): void
    {
        $address = Address::fromScalars(
            'Main St',
            '123',
            'Apt 4',
            null,
            'New York',
            'NY',
            '10001',
            'd4cc6ee6-0722-400b-9f60-482e595065b0',
            'US',
            40.712776,
            -74.005974,
        );
        $this->assertInstanceOf(Address::class, $address);
        $this->assertInstanceOf(Street::class, $address->street());
        $this->assertInstanceOf(AdministrativeArea::class, $address->administrativeArea1());
        $this->assertInstanceOf(AdministrativeArea::class, $address->administrativeArea2());
        $this->assertInstanceOf(PostalCode::class, $address->postalCode());
        $this->assertInstanceOf(Location::class, $address->location());
        $this->assertInstanceOf(Country::class, $address->country());
        $this->assertInstanceOf(Coordinates::class, $address->coordinates());
        $this->assertEquals('Main St', $address->street()->name());
        $this->assertEquals('123', $address->street()->number());
        $this->assertEquals('Apt 4', $address->street()->other1());
        $this->assertNull($address->street()->other2());
        $this->assertEquals('New York', $address->administrativeArea1()->toString());
        $this->assertEquals('NY', $address->administrativeArea2()->toString());
        $this->assertEquals('10001', $address->postalCode()->toString());
        $this->assertEquals('d4cc6ee6-0722-400b-9f60-482e595065b0', $address->location()->toString());
        $this->assertEquals('US', $address->country()->toString());
        $this->assertEquals(40.712776, $address->coordinates()->latitude());
        $this->assertEquals(-74.005974, $address->coordinates()->longitude());
    }

    public function testCanCreateFromScalarsWithoutOptionalValues(): void
    {
        $address = Address::fromScalars(
            'Main St',
            '123',
            null,
            null,
            'New York',
            'NY',
            null,
            'd4cc6ee6-0722-400b-9f60-482e595065b0',
            'US',
            null,
            null,
        );
        $this->assertInstanceOf(Address::class, $address);
        $this->assertInstanceOf(Street::class, $address->street());
        $this->assertInstanceOf(AdministrativeArea::class, $address->administrativeArea1());
        $this->assertInstanceOf(AdministrativeArea::class, $address->administrativeArea2());
        $this->assertNull($address->postalCode());
        $this->assertInstanceOf(Location::class, $address->location());
        $this->assertInstanceOf(Country::class, $address->country());
        $this->assertNull($address->coordinates());
        $this->assertFalse($address->hasCoordinates());
        $this->assertEquals('Main St', $address->street()->name());
        $this->assertEquals('123', $address->street()->number());
        $this->assertNull($address->street()->other1());
        $this->assertNull($address->street()->other2());
        $this->assertEquals('New York', $address->administrativeArea1()->toString());
        $this->assertEquals('NY', $address->administrativeArea2()->toString());
        $this->assertEquals('d4cc6ee6-0722-400b-9f60-482e595065b0', $address->location()->toString());
        $this->assertEquals('US', $address->country()->toString());
    }
}
