#!/usr/bin/env php
<?php

declare(strict_types=1);

use Boilerwork\Support\ValueObjects\Image;
use Boilerwork\Support\MultiLingualText;
use Boilerwork\Validation\CustomAssertionFailedException;
use PHPUnit\Framework\TestCase;

final class ImageTest extends TestCase
{

    /**
     * @test
     * @dataProvider imageURL
     * @covers \App\Core\Packages\Domain\Model\Package\ValueObjects\Name
     **/
    public function testItCanBeCreatedFromScalars(): void
    {
        $image = Image::fromScalars('https://example.com/image.jpg', 'An example image', 'EN');

        $this->assertInstanceOf(Image::class, $image);
    }

    public function testItCanBeCreatedFromArray(): void
    {
        $image = Image::fromArray('https://example.com/image.jpg', ['EN' => 'An example image']);

        $this->assertInstanceOf(Image::class, $image);
    }

    public function testItCanBeRepresentedAsArray(): void
    {
        $image = Image::fromScalars('https://example.com/image.jpg', 'An example image', 'EN');

        $this->assertEquals([
            'url' => 'https://example.com/image.jpg',
            'description' => 'An example image',
        ], $image->toArray('EN'));
    }

    public function testItThrowsAnExceptionWhenInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Url must be a valid URL format');

        Image::fromScalars('invalid', 'An example image', 'EN');
    }
}
