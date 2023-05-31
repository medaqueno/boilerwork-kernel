<?php

declare(strict_types=1);

use Boilerwork\Support\MultiLingualText;
use Boilerwork\Support\ValueObjects\Language\Language;
use PHPUnit\Framework\TestCase;
use Boilerwork\Validation\CustomAssertionFailedException;

final class MultiLingualTextTest extends TestCase
{

    /**
     * @test
     * @covers Boilerwork\Support\MultiLingualText
     */
    public function testMultiLingualTextCreation(): void
    {
        $text = 'Hola Mundo';
        $language = Language::FALLBACK;
        $multiLingualText = MultiLingualText::fromSingleLanguageString($text, $language);

        $this->assertInstanceOf(
            MultiLingualText::class,
            $multiLingualText
        );

        $this->assertEquals(
            $text,
            $multiLingualText->toStringByLang($language)
        );
    }

    /**
     * @test
     * @covers \Boilerwork\Support\MultiLingualText
     */
    public function testInvalidLanguageWithSingleLanguageString(): void
    {
//        $this->expectException(CustomAssertionFailedException::class);
//        $this->expectExceptionMessage('language.invalidIso3166Alpha2');
        MultiLingualText::fromSingleLanguageString('Hello World', 'invalid');
    }

}
