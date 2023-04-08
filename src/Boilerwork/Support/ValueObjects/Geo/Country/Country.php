<?php

declare(strict_types=1);

namespace Boilerwork\Support\ValueObjects\Geo\Country;

use Boilerwork\Foundation\ValueObjects\ValueObject;
use Boilerwork\Support\MultiLingualText;
use Boilerwork\Support\ValueObjects\Geo\Coordinates;
use Boilerwork\Support\ValueObjects\Language\Language;
use Boilerwork\Validation\Assert;

use function var_dump;

class Country extends ValueObject
{
    private function __construct(
        private readonly MultiLingualText $name,
        private readonly ?Iso31661Alpha2 $iso31661Alpha2,
        private readonly ?Iso31661Alpha3 $iso31661Alpha3,
        private readonly ?Coordinates $coordinates,
    ) {

        $names = $name->toArray();
        foreach ($names as $name) {
            Assert::lazy()->tryAll()
                ->that($name)
                ->string('Value must be a string', 'countryName.invalidType')
                ->notEmpty('Value must not be empty', 'countryName.notEmpty')
                ->maxLength(128, 'Value must be 128 characters length', 'countryName.invalidLength')
                ->verifyNow();
        }

        Assert::lazy()->tryAll()
            ->that($iso31661Alpha2)
            ->satisfy(function () use ($iso31661Alpha2, $iso31661Alpha3) {
                return $iso31661Alpha2 !== null || $iso31661Alpha3 !== null;
            }, 'At least one ISO 31661-X must be provided', 'country.notFoundIso')
            ->verifyNow();
    }

    public static function fromScalars(
        array $name,
        string $iso31661Alpha2,
        string $iso31661Alpha3,
        ?float $latitude,
        ?float $longitude,
    ): self {
        return new self(
            name: MultiLingualText::fromArray($name),
            iso31661Alpha2: Iso31661Alpha2::fromString($iso31661Alpha2),
            iso31661Alpha3: Iso31661Alpha3::fromString($iso31661Alpha3),
            coordinates: ($latitude !== null && $longitude !== null) ? Coordinates::fromScalars(
                $latitude,
                $longitude,
            ) : null
        );
    }

    public static function fromScalarsWithIso31661Alpha2(
        array $name,
        string $iso31661Alpha2,
        ?float $latitude,
        ?float $longitude,
    ): self {
        return new self(
            name: MultiLingualText::fromArray($name),
            iso31661Alpha2: Iso31661Alpha2::fromString($iso31661Alpha2),
            iso31661Alpha3: null,
            coordinates: ($latitude !== null && $longitude !== null) ? Coordinates::fromScalars(
                $latitude,
                $longitude,
            ) : null
        );
    }

    public static function fromScalarsWithIso31661Alpha3(
        array $name,
        string $iso31661Alpha3,
        ?float $latitude,
        ?float $longitude,
    ): self {
        return new self(
            name: MultiLingualText::fromArray($name),
            iso31661Alpha2: null,
            iso31661Alpha3: Iso31661Alpha3::fromString($iso31661Alpha3),
            coordinates: ($latitude !== null && $longitude !== null) ? Coordinates::fromScalars(
                $latitude,
                $longitude,
            ) : null
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

    public function iso31661Alpha2(): ?Iso31661Alpha2
    {
        return $this->iso31661Alpha2;
    }

    public function iso31661Alpha3(): ?Iso31661Alpha3
    {
        return $this->iso31661Alpha3;
    }

    public function coordinates(): ?Coordinates
    {
        return $this->coordinates;
    }

    /**
     * @see Coordinates::toArray()
     */
    public function toArray(?string $lang = null): array
    {
        return [
            'name'           => $lang ? $this->name($lang) : $this->toNames()->toArray(),
            'iso31661Alpha2' => $this->iso31661Alpha2?->toString(),
            'iso31661Alpha3' => $this->iso31661Alpha3?->toString(),
            'coordinates'    => $this->coordinates?->toArray(),
        ];
    }

    public function toString(string $language = Language::FALLBACK): ?string
    {
        return $this->name($language);
    }
}
