#!/usr/bin/env php
<?php

declare(strict_types=1);

use Boilerwork\Support\ValueObjects\EmailAddress;
use Boilerwork\Validation\CustomAssertionFailedException;
use PHPUnit\Framework\TestCase;

// use Deminy\Counit\TestCase;

final class EmailAddressTest extends TestCase
{
    private function testedClass(string $email)
    {
        return $this->getMockForAbstractClass(
            EmailAddress::class,
            [$email]
        );
    }

    public function providerEmail(): iterable
    {
        yield 'valid@emailaddress.com' => [
            'valid@emailaddress.com'
        ];
        yield 'VAlid@emailAddress.com' => [
            'VAlid@emailAddress.com'
        ];
        yield 'another@pangea.es' => [
            'another@pangea.es'
        ];
        yield 'gmail+like@gmail.com' => [
            'another@pangea.es'
        ];
    }

    public function providerInvalidEmail(): iterable
    {
        yield 'invalid.com' => [
            'invalid.com',
        ];
        yield 'invalid@.com' => [
            'invalid@.com',
        ];
        yield 'invalid@.rtfa' => [
            'invalid@.rtfa',
        ];
        yield 'as++-"·dad@asdf.com' => [
            'as++-"·dad@asdf.com',
        ];
    }

    /**
     * @test
     * @dataProvider providerEmail
     * @covers \Boilerwork\Support\ValueObjects\EmailAddress
     **/
    public function testNewEmail($email): void
    {
        $emailAddress = $this->testedClass($email);

        $this->assertInstanceOf(
            EmailAddress::class,
            $emailAddress
        );

        $this->assertSame(
            $emailAddress->toString(),
            mb_strtolower($email)
        );
    }

    /**
     * @test
     * @dataProvider providerInvalidEmail
     * @covers \Boilerwork\Support\ValueObjects\EmailAddress
     **/
    public function testInvalidEmail(string $email): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('email.invalidFormat');

        $this->testedClass($email);
    }

    /**
     * @test
     * @covers \Boilerwork\Support\ValueObjects\EmailAddress
     **/
    public function testGetAccount(): void
    {
        $email = $this->testedClass('another@pangea.es');
        $this->assertEquals($email->account(), 'another');
    }

    /**
     * @test
     * @covers \Boilerwork\Support\ValueObjects\EmailAddress
     **/
    public function testGetDomain(): void
    {
        $email = $this->testedClass('another@pangea.es');
        $this->assertEquals($email->domain(), 'pangea.es');
    }
}
