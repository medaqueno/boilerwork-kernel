<?php

declare(strict_types=1);

namespace Boilerwork\Support\ValueObjects\Geo;

use Boilerwork\Foundation\ValueObjects\ValueObject;
use Boilerwork\Support\MultiLingualText;
use Boilerwork\Support\ValueObjects\Geo\Country\Iso31661Alpha2;
use Boilerwork\Support\ValueObjects\Language\Language;
use Boilerwork\Validation\Assert;

class Location extends ValueObject
{
    private function __construct(
        private readonly MultiLingualText $name,
        private readonly Iso31661Alpha2 $iso31661Alpha2,
        private readonly Coordinates $coordinates,
    ) {
        $names = $name->toArray();
        foreach ($names as $name) {
            Assert::lazy()->tryAll()
                ->that($name)
                ->string('Value must be a string', 'name.invalidType')
                ->notEmpty('Value must not be empty', 'name.notEmpty')
                ->maxLength(128, 'Value must be 128 characters length', 'name.invalidLength')
                ->verifyNow();
        }
    }

    public static function fromScalars(
        array $name,
        string $iso31661Alpha2,
        float $latitude,
        float $longitude,
    ): self {
        return new self(
            name: MultiLingualText::fromArray($name),
            iso31661Alpha2: Iso31661Alpha2::fromString($iso31661Alpha2),
            coordinates: Coordinates::fromScalars($latitude, $longitude),
        );
    }

    public function toNames(): MultiLingualText
    {
        return $this->name;
    }

    public function name(string $language = Language::FALLBACK): ?string
    {
        return $this->name->toStringByLang($language);
    }

    public function iso31661Alpha2(): Iso31661Alpha2
    {
        return $this->iso31661Alpha2;
    }

    public function coordinates(): Coordinates
    {
        return $this->coordinates;
    }

    public function toArray(?string $lang = null): array
    {
        return [
            'name'           => $lang ? $this->name($lang) : $this->toNames()->toArray(),
            'iso31661Alpha2' => $this->iso31661Alpha2()->toString(),
            'coordinates'    => $this->coordinates->toArray(),
        ];
    }

    public function toString(string $language = Language::FALLBACK): ?string
    {
        return $this->name($language);
    }
}
