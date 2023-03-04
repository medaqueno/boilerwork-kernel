<?php

declare(strict_types=1);

use Boilerwork\Support\ValueObjects\Address\Address;
use Boilerwork\Support\ValueObjects\Address\AdministrativeArea;
use Boilerwork\Support\ValueObjects\Address\Location;
use Boilerwork\Support\ValueObjects\Address\PostalCode;
use Boilerwork\Support\ValueObjects\Address\Street;
use Boilerwork\Support\ValueObjects\Country\Country;
use Boilerwork\Support\ValueObjects\Country\Iso31661Alpha2Code;
use PHPUnit\Framework\TestCase;
use Boilerwork\Support\ValueObjects\Geo\Coordinates;

class AddressTest extends TestCase
{
    public function testCanCreateAddressObject(): void
    {
        $street = Street::fromValues('Main St.');
        $administrativeArea1 = AdministrativeArea::fromString('New York');
        $administrativeArea2 = AdministrativeArea::fromString('Manhattan');
        $postalCode = PostalCode::fromString('28805', Iso31661Alpha2Code::fromString('ES'));
        $location = Location::fromString('Madrid');
        $country = Country::fromIso31661Alpha2Code(Iso31661Alpha2Code::fromString('ES'));
        $coordinates = Coordinates::fromValues(43, -32.34);

        $address = Address::fromScalars(
            street: $street,
            administrativeArea1: $administrativeArea1,
            administrativeArea2: $administrativeArea2,
            postalCode: $postalCode,
            location: $location,
            country: $country,
            coordinates: $coordinates
        );

        $this->assertInstanceOf(Address::class, $address);
    }
}
