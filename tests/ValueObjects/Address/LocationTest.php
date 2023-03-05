<?php

declare(strict_types=1);

use Boilerwork\Support\ValueObjects\Address\Location;
use Boilerwork\Support\ValueObjects\Identity;
use Boilerwork\Validation\CustomAssertionFailedException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Boilerwork\Support\ValueObjects\Address\Location
 */
class LocationTest extends TestCase
{
    /**
     * @test
     * @group address
     */
    public function it_can_be_created_from_string(): void
    {
        $id = Identity::create();
        $location = Location::fromId($id->toString());
        $this->assertEquals($id->toString(), $location->toString());
    }

    /**
     * @test
     * @group address
     */
    public function it_throws_custom_exception_when_id_is_invalid(): void
    {
        $this->expectException(CustomAssertionFailedException::class);

        Location::fromId('invalid_id');
    }
}
