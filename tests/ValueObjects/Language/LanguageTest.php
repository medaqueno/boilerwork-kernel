#!/usr/bin/env php
<?php

declare(strict_types=1);

use Boilerwork\Support\ValueObjects\Language\Iso6391Code;
use Boilerwork\Support\ValueObjects\Language\Language;
use PHPUnit\Framework\TestCase;
use Boilerwork\Validation\CustomAssertionFailedException;
// use Deminy\Counit\TestCase;

final class LanguageTest extends TestCase
{
    public function fromStringProvider(): iterable
    {
        yield "from string" => [
            Language::fromIsoCode('en'), // Lowercase
            Language::fromIsoCode('FR'), // Uppercase
        ];
    }

    public function fromIso6391CodeProvider(): iterable
    {
        yield "from isocode" => [
            Language::fromIso6391Code(new Iso6391Code('en')), // Lowercase
            Language::fromIso6391Code(new Iso6391Code('FR')), // Uppercase
        ];
    }

    /**
     * @test
     * @dataProvider fromStringProvider
     * @covers \App\Core\Shared\ValueObjects\Language\Language
     **/
    public function testFromString(Language $language): void
    {
        $this->assertInstanceOf(
            Language::class,
            $language
        );
    }

    /**
     * @test
     * @dataProvider fromIso6391CodeProvider
     * @covers \App\Core\Shared\ValueObjects\Language\Language
     * @covers \App\Core\Shared\ValueObjects\Language\Iso6391Code
     **/
    public function testFromIso6391Code(Language $language): void
    {
        $this->assertInstanceOf(
            Language::class,
            $language
        );
    }

    /**
     * @test
     * @covers \App\Core\Shared\ValueObjects\Language\Language
     * @covers \App\Core\Shared\ValueObjects\Language\Iso6391Code
     **/
    public function testInvalidValue()
    {
        $this->expectException(CustomAssertionFailedException::class);
        Language::fromIso6391Code(new Iso6391Code('mm'));
    }

    /**
     * @test
     * @covers \App\Core\Shared\ValueObjects\Language\Language
     * @covers \App\Core\Shared\ValueObjects\Language\Iso6391Code
     **/
    public function testLanguageName()
    {
        $lang = Language::fromIso6391Code(new Iso6391Code('en'));

        $this->assertEquals('English', $lang->name());
    }

    /**
     * @test
     * @covers \App\Core\Shared\ValueObjects\Language\Language
     * @covers \App\Core\Shared\ValueObjects\Language\Iso6391Code
     **/
    public function testLanguageToPrimitive()
    {
        $lang = Language::fromIso6391Code(new Iso6391Code('en'));

        $this->assertEquals('en', $lang->toPrimitive());
    }
}
