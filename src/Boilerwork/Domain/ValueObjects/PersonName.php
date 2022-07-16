#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Domain\ValueObjects;

use Boilerwork\Domain\ValueObjects\ValueObject;
use Boilerwork\Domain\Assert;

/**
 * @internal
 **/
abstract class PersonName extends ValueObject
{
    private string $latinUnicodePattern = '/^([a-zA-ZÀ-ÖÙ-öù-ÿĀ-žḀ-ỿ\s\-\']+)$/';

    private string $latinUnicodeOrEmptyStringPattern = '/^(^$|[a-zA-ZÀ-ÖÙ-öù-ÿĀ-žḀ-ỿ\s\-\']+)$/';

    public function __construct(
        public readonly string $firstName,
        public readonly string $middleName,
        public readonly string $lastName,
        public readonly string $lastName2,
    ) {
        Assert::lazy()->tryAll()
            ->that($firstName)
            ->regex($this->latinUnicodePattern, 'Only latin unicode letters and - \' allowed', 'personName.invalidValue')
            ->maxLength(64, 'Maximum 64 characters length', 'personName.maxLength')
            ->that($lastName)
            ->regex($this->latinUnicodePattern, 'Only latin unicode letters and - \' allowed', 'personName.invalidValue')
            ->maxLength(64, 'Maximum 64 characters length', 'personName.maxLength')
            ->that($middleName)
            ->regex($this->latinUnicodeOrEmptyStringPattern, 'Only latin unicode letters and - \' allowed or empty string', 'personName.invalidValue')
            ->maxLength(64, 'Maximum 64 characters length', 'personName.maxLength')
            ->that($lastName2)
            ->regex($this->latinUnicodeOrEmptyStringPattern, 'Only latin unicode letters and - \' allowed or empty string', 'personName.invalidValue')
            ->maxLength(64, 'Maximum 64 characters length', 'personName.maxLength')
            ->verifyNow();
    }

    public function equals(ValueObject $object): bool
    {
        return $this->toPrimitive() === $object->toPrimitive() && $object instanceof self;
    }

    /**
     * Name string is returned in the following format and order:
     * (Given names + Inherited names)
     * firstName middleName lastName lastName2
     *
     * @return string
     */
    public function toPrimitive(): string
    {
        return sprintf('%s %s %s %s', $this->firstName, $this->middleName, $this->lastName, $this->lastName2);
    }

    /**
     * First name of a person.
     */
    public function firstName(): string
    {
        return $this->firstName;
    }

    /**
     * Middle name
     * Typical in English speaking countries.
     */
    public function middleName(): string
    {
        return $this->middleName;
    }

    /**
     * Last name of a person.
     */
    public function lastName(): string
    {
        return $this->lastName;
    }

    /**
     * Second Last name of a person.
     * (Segundo Apellido: In Spain and other Spanish spaking countries, as well as in Portugal)
     */
    public function lastName2(): string
    {
        return $this->lastName2;
    }
}
