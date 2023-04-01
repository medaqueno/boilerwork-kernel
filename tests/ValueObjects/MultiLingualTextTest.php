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
            $multiLingualText->getTextByLanguage($language)
        );
    }

    /**
     * @test
     * @covers \Boilerwork\Support\MultiLingualText
     */
    public function testInvalidLanguage(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('language.invalidIso3166Alpha2');
        MultiLingualText::fromSingleLanguageString('Hello World', 'invalid');
    }

    /**
     * @test
     * @covers \Boilerwork\Support\MultiLingualText
     */
    public function testInvalidText(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('text.invalidText');
        MultiLingualText::fromSingleLanguageString('', Language::FALLBACK);
    }

    /**
     * @test
     * @covers \Boilerwork\Support\MultiLingualText
     */
    public function testAddText(): void
    {
        $text1 = 'Hola Mundo';
        $language1 = Language::FALLBACK;
        $text2 = 'Hello World';
        $language2 = 'EN';

        $multiLingualText = MultiLingualText::fromSingleLanguageString($text1, $language1);
        $multiLingualText = $multiLingualText->addText($text2, $language2);

        $this->assertEquals(
            $text1,
            $multiLingualText->getTextByLanguage($language1)
        );

        $this->assertEquals(
            $text2,
            $multiLingualText->getTextByLanguage($language2)
        );
    }

    /**
     * @test
     * @covers \Boilerwork\Support\MultiLingualText
     */
    public function testFromArrayAndToArray(): void
    {
        $texts = [
            'EN' => 'Hello World',
            'ES' => 'Hola Mundo',
        ];

        $multiLingualText = MultiLingualText::fromArray($texts);

        $this->assertEquals(
            $texts,
            $multiLingualText->toArray()
        );
    }

    /**
     * @test
     * @covers \Boilerwork\Support\MultiLingualText
     */
    public function testFromJson(): void
    {
        $json = '{"EN": "Hello World", "ES": "Hola Mundo"}';
        $multiLingualText = MultiLingualText::fromJson($json);

        $this->assertEquals(
            'Hello World',
            $multiLingualText->getTextByLanguage('EN')
        );

        $this->assertEquals(
            'Hola Mundo',
            $multiLingualText->getTextByLanguage('ES')
        );
    }

    /**
     * @test
     * @covers \Boilerwork\Support\MultiLingualText
     */
    public function testInvalidJson(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('texts.invalidJson');
        MultiLingualText::fromJson('{"EN": "Hello World",}');
    }

    /**
     * @test
     * @covers \Boilerwork\Support\MultiLingualText
     */
    public function testToJson(): void
    {
        $texts = [
            'EN' => 'Hello World',
            'ES' => 'Hola Mundo',
        ];

        $multiLingualText = MultiLingualText::fromArray($texts);
        $json = $multiLingualText->toJson();

        $this->assertJson($json);
        $this->assertJsonStringEqualsJsonString('{"EN": "Hello World", "ES": "Hola Mundo"}', $json);
    }

    /**
     * @test
     * @covers \Boilerwork\Support\MultiLingualText
     */
    public function testNonExistentLanguage(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('language.invalidIso3166Alpha2');

        $texts = [
            'EN' => 'Hello World',
            'ES' => 'Hola Mundo',
        ];

        $multiLingualText = MultiLingualText::fromArray($texts);
        $multiLingualText->getTextByLanguage('FR');
    }

    /**
     * @test
     * @covers \Boilerwork\Support\MultiLingualText
     */
    public function testFallbackLanguage(): void
    {


        $texts = [
            'ES' => 'Hola Mundo',
        ];

        $multiLingualText = MultiLingualText::fromArray($texts);

        $this->assertEquals(
            'Hola Mundo',
            $multiLingualText->getTextByLanguage('EN', Language::FALLBACK)
        );
    }

    /**
     * @test
     * @covers \Boilerwork\Support\MultiLingualText
     */
    public function testToString(): void
    {
        $texts = [
            'EN' => 'Hello World',
            'ES' => 'Hola Mundo',
        ];

        $multiLingualText = MultiLingualText::fromArray($texts);

        $this->assertEquals(
            'Hola Mundo',
            (string) $multiLingualText
        );
    }
}
