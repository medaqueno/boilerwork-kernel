#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\ValueObjects;

use Boilerwork\Foundation\ValueObjects\ValueObject;
use Boilerwork\Validation\Assert;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

/**
 * Depends on: https://github.com/giggsey/libphonenumber-for-php
 **/
final class Phone extends ValueObject
{
    /**
     * @example: new Phone(countryCallingCode: PhonePrefix::fromCountryCallingCode('+34'), number: "910837976"), new Phone(countryCallingCode: PhonePrefix::fromCountryCallingCode(null), number: "910837976"),
     * @see https://www.itu.int/rec/T-REC-E.164/es
     **/
    public function __construct(
        private readonly PhonePrefix $countryCallingCode,
        private readonly string $number,
    ) {
        Assert::lazy()->tryAll()
            ->that($number)
            ->notEmpty(
                'Must be a valid E164 format number',
                'phoneNumber.emptyValue'
            )
            ->that(sprintf('%s%s', $countryCallingCode->toPrimitive(), $number))
            ->e164(
                'Must be a valid E164 format number',
                'phoneNumber.invalidValue'
            )
            ->verifyNow();
    }

    public function number(): string
    {
        return $this->number;
    }

    public function countryCallingCode(): PhonePrefix
    {
        return $this->countryCallingCode;
    }

    /**
     * +49 211 5684962
     */
    public function formatInternational(): string
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        return $phoneUtil->format($this->getProtoNumber(), PhoneNumberFormat::INTERNATIONAL);
    }

    /**
     * 
     */
    public function formatRFC3966(): string
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        return $phoneUtil->format($this->getProtoNumber(), PhoneNumberFormat::RFC3966);
    }

    /**
     * +492115684962
     */
    public function formatE164(): string
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        return $phoneUtil->format($this->getProtoNumber(), PhoneNumberFormat::E164);
    }

    /**
     * 0211 5684962
     */
    public function formatNational(): string
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        return $phoneUtil->format($this->getProtoNumber(), PhoneNumberFormat::NATIONAL);
    }

    /**
     * Format E164: 0211 5684962
     */
    public function toPrimitive(): string
    {
        return $this->formatE164();
    }

    private function getProtoNumber(): PhoneNumber
    {
        return (new PhoneNumber())->setNationalNumber($this->number)->setCountryCode($this->countryCallingCode->toPrimitive());
    }
}
