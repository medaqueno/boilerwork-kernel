#!/usr/bin/env php
<?php

declare(strict_types=1);

use Boilerwork\Support\ValueObjects\PersonName;
use Boilerwork\Validation\CustomAssertionFailedException;
use PHPUnit\Framework\TestCase;
// use Deminy\Counit\TestCase;

final class PersonNameTest extends TestCase
{
    private function testedClass(string $firstName, string $middleName, string $lastName, string $lastName2)
    {
        return $this->getMockForAbstractClass(
            PersonName::class,
            [$firstName, $middleName, $lastName, $lastName2]
        );
    }

    public function providerPersonName(): iterable
    {
        yield 'Charles Robert Darwin' => [
            [
                'firstName' => 'Charles',
                'middleName' => 'Robert',
                'lastName' => 'Darwin',
                'lastName2' => '',
            ],
        ];
        yield 'Albert Camus' => [
            [
                'firstName' => 'Albert',
                'middleName' => '',
                'lastName' => 'Camus',
                'lastName2' => '',
            ],
        ];
        yield 'Vicente Blasco Ibáñez' => [
            [
                'firstName' => 'Vicente',
                'middleName' => '',
                'lastName' => 'Blasco',
                'lastName2' => 'Ibáñez',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider providerPersonName
     * @covers \App\Core\Authentication\Domain\Model\User\ValueObjects\PersoName
     **/
    public function testNewUserNameAndExtendsKernelPersonName(array $personName): void
    {
        $personName = $this->testedClass(
            firstName: $personName['firstName'],
            middleName: $personName['middleName'],
            lastName: $personName['lastName'],
            lastName2: $personName['lastName2'],
        );

        $this->assertInstanceOf(
            PersonName::class,
            $personName
        );
    }

    /**
     * @test
     * @covers \App\Core\Authentication\Domain\Model\User\ValueObjects\PersoName
     **/
    public function testInvalidFirstNamePersonName(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('personNameFirstName.invalidValue');

        $personName = $this->testedClass(
            firstName: 'Char$5%&les',
            middleName: '',
            lastName: 'Darwin',
            lastName2: '',
        );
    }

    /**
     * @test
     * @covers \App\Core\Authentication\Domain\Model\User\ValueObjects\PersoName
     **/
    public function testInvalidLastNamePersonName(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('personNameLastName.invalidValue');

        $this->testedClass(
            firstName: 'Charles',
            middleName: '',
            lastName: 'Dar99.win',
            lastName2: '',
        );
    }

    /**
     * @test
     * @covers \App\Core\Authentication\Domain\Model\User\ValueObjects\PersoName
     **/
    public function testInvalidLengthLastNamePersonName(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('personNameLastName.maxLength');

        $this->testedClass(
            firstName: 'Charles',
            middleName: '',
            lastName: 'Darwin lkjad ñkla  ñlkjasl ñkjlksdlkj adkj akjh sdkjh s dkjh sdfkjh kjh',
            lastName2: '',
        );
    }

    /**
     * @test
     * @dataProvider providerPersonName
     * @covers \App\Core\Authentication\Domain\Model\User\ValueObjects\PersonName::toArray
     **/
    public function testToArray(array $personName): void
    {
        $personNameObject = $this->testedClass(
            firstName: $personName['firstName'],
            middleName: $personName['middleName'],
            lastName: $personName['lastName'],
            lastName2: $personName['lastName2'],
        );

        $expectedArray = [
            'firstName' => $personName['firstName'],
            'middleName' => $personName['middleName'],
            'lastName' => $personName['lastName'],
            'lastName2' => $personName['lastName2'],
        ];

        $this->assertSame($expectedArray, $personNameObject->toArray());
    }

}
