<?php

declare(strict_types=1);

namespace Boilerwork\Support\ValueObjects\Geo\Country;

use Boilerwork\Foundation\ValueObjects\ValueObject;
use Boilerwork\Support\MultiLingualText;
use Boilerwork\Support\ValueObjects\Geo\Coordinates;
use Boilerwork\Support\ValueObjects\Language\Language;
use Boilerwork\Validation\Assert;

class Country extends ValueObject
{
    private function __construct(
        private readonly MultiLingualText $name,
        private readonly ?Iso31661Alpha2 $iso31661Alpha2,
        private readonly ?Iso31661Alpha3 $iso31661Alpha3,
        private readonly ?Coordinates $coordinates
    ) {
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
        ?float $longitude
    ): self {

        return new self(
            name: MultiLingualText::fromArray($name),
            iso31661Alpha2: Iso31661Alpha2::fromString($iso31661Alpha2),
            iso31661Alpha3: Iso31661Alpha3::fromString($iso31661Alpha3),
            coordinates: ($latitude !== null && $longitude !== null) ? Coordinates::fromScalars($latitude, $longitude) : null
        );
    }

    public static function fromScalarsWithIso31661Alpha2(
        array $name,
        string $iso31661Alpha2,
        ?float $latitude,
        ?float $longitude
    ): self {
        return new self(
            name: MultiLingualText::fromArray($name),
            iso31661Alpha2: Iso31661Alpha2::fromString($iso31661Alpha2),
            iso31661Alpha3: null,
            coordinates: ($latitude !== null && $longitude !== null) ? Coordinates::fromScalars($latitude, $longitude) : null
        );
    }

    public static function fromScalarsWithIso31661Alpha3(
        array $name,
        string $iso31661Alpha3,
        ?float $latitude,
        ?float $longitude
    ): self {
        return new self(
            name: MultiLingualText::fromArray($name),
            iso31661Alpha2: null,
            iso31661Alpha3: Iso31661Alpha3::fromString($iso31661Alpha3),
            coordinates: ($latitude !== null && $longitude !== null) ? Coordinates::fromScalars($latitude, $longitude) : null
        );
    }

    public function toString(string $language = Language::FALLBACK): string
    {
        return $this->name($language);
    }

    public function names(): MultiLingualText
    {
        return $this->name;
    }

    public function nameByLanguage(?string $language): string
    {
        return $this->name->getTextByLanguage($language);
    }

    public function name(): string
    {
        return $this->name->getDefaultText();
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
     * @return array{
     *     name: string|null,
     *     iso31661Alpha2: string|null,
     *     iso31661Alpha3: string|null,
     *     coordinates: array{ latitude: float, longitude: float }|null
     * }
     * @see Coordinates::toArray()
     */
    public function toArray(string $language = null): array
    {
        return [
            'name' => $language ? $this->name->getTextByLanguage($language) : $this->name->getDefaultText(),
            'iso31661Alpha2' => $this->iso31661Alpha2?->toString(),
            'iso31661Alpha3' => $this->iso31661Alpha3?->toString(),
            'coordinates' => $this->coordinates?->toArray(),
        ];
    }
}
