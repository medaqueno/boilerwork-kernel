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
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('language.invalidIso3166Alpha2');
        MultiLingualText::fromSingleLanguageString('Hello World', 'invalid');
    }

    /**
     * @test
     * @covers \Boilerwork\Support\MultiLingualText
     */
    public function testnotEmpty(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('text.notEmpty');
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
            $multiLingualText->toStringByLang($language1)
        );

        $this->assertEquals(
            $text2,
            $multiLingualText->toStringByLang($language2)
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
            $multiLingualText->toStringByLang('EN')
        );

        $this->assertEquals(
            'Hola Mundo',
            $multiLingualText->toStringByLang('ES')
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
    public function testGetNonExistentLanguage(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('language.invalidIso3166Alpha2');

        $texts = [
            'EN' => 'Hello World',
            'ES' => 'Hola Mundo',
        ];

        $multiLingualText = MultiLingualText::fromArray($texts);
        $multiLingualText->toStringByLang('FR');
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
            $multiLingualText->toStringByLang()
        );
    }

    public function testCreateFromSingleLanguageStringWithInvalidLanguage(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('language.invalidIso3166Alpha2');
        \Boilerwork\Support\MultiLingualText::fromSingleLanguageString('Hola', 'XXX');
    }

    public function testCreateFromSingleLanguageStringWithEmptyText(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('text.notEmpty');
        \Boilerwork\Support\MultiLingualText::fromSingleLanguageString('');
    }

    public function testAddTextWithInvalidLanguage(): void
    {
        $text = \Boilerwork\Support\MultiLingualText::fromArray(['ES' => 'Hola']);

        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('language.invalidIso3166Alpha2');
        $text->addText('Hello', 'XXX');
    }

    public function testAddTextWithEmptyText(): void
    {
        $text = \Boilerwork\Support\MultiLingualText::fromArray(['ES' => 'Hola']);

        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('text.notEmpty');
        $text->addText('');
    }

    public function testCreateFromArrayWithInvalidLanguage(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('language.invalidIso3166Alpha2');

        MultiLingualText::fromArray(['DE' => 'Hola']);
    }

    public function testCreateFromArrayWithEmptyText(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('text.notEmpty');
        MultiLingualText::fromArray(['ES' => '']);
    }
}
