<?php

namespace Boilerwork\Support\ValueObjects\Language;

use Boilerwork\Foundation\ValueObjects\ValueObject;
use Boilerwork\Validation\Assert;

use function array_keys;
use function strtoupper;

final class Iso6391Code extends ValueObject
{
    public function __construct(
        private string $value
    ) {
        $this->value = $value = strtoupper($value);

        Assert::lazy()->tryAll()
            ->that($value)
            ->inArray(array_keys(LanguageDataProvider::data()), 'Value must be a valid ISO-6391 code', 'languageCode.invalidValue')
            ->verifyNow();
    }

    public function toPrimitive(): string
    {
        return $this->value;
    }

    public function equals(ValueObject $object): bool
    {
        return $this->value === $object->toPrimitive() && $object instanceof self;
    }
}