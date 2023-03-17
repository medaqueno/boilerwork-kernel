<?php

declare(strict_types=1);

use Boilerwork\Support\ValueObjects\Geo\Country\Country;
use Boilerwork\Validation\CustomAssertionFailedException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Boilerwork\Support\ValueObjects\Geo\Country\Country
 * @group geo
 */
class CountryTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_created_from_scalars(): void
    {
        $country = Country::fromScalars('Spain', 'ES', 'ESP', 40.463667, -3.74922);

        $this->assertSame('Spain', $country->name());
        $this->assertSame('ES', $country->iso31661Alpha2()->toString());
        $this->assertSame('ESP', $country->iso31661Alpha3()->toString());
    }

    /**
     * @test
     */
    public function it_can_be_created_from_scalars_with_iso31661_alpha2(): void
    {
        $country = Country::fromScalarsWithIso31661Alpha2('Spain', 'ES', 40.463667, -3.74922);

        $this->assertSame('Spain', $country->name());
        $this->assertSame('ES', $country->iso31661Alpha2()->toString());
        $this->assertNull($country->iso31661Alpha3());
    }

    /**
     * @test
     */
    public function it_can_be_created_from_scalars_with_iso31661_alpha3(): void
    {
        $country = Country::fromScalarsWithIso31661Alpha3('Spain', 'ESP', 40.463667, -3.74922);

        $this->assertSame('Spain', $country->name());
        $this->assertNull($country->iso31661Alpha2());
        $this->assertSame('ESP', $country->iso31661Alpha3()->toString());
    }

    /**
     * @test
     */
    public function it_can_not_be_created_with_empty_name(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('country.invalidName');

        Country::fromScalars('', 'ES', 'ESP', 40.463667, -3.74922);
    }

    /**
     * @test
     */
    public function it_can_not_be_created_without_iso31661_alpha2_and_alpha3(): void
    {
        $this->expectException(TypeError::class);

        Country::fromScalars('Spain', null, null, null, null);
        Country::fromScalarsWithIso31661Alpha2('Spain', null, null, null);
        Country::fromScalarsWithIso31661Alpha3('Spain', null, null, null);
    }
}
