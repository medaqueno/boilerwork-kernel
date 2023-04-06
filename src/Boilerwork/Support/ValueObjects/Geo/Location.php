<?php

declare(strict_types=1);

namespace Boilerwork\Support\ValueObjects\Geo;

use Boilerwork\Foundation\ValueObjects\ValueObject;
use Boilerwork\Support\MultiLingualText;
use Boilerwork\Support\ValueObjects\Geo\Coordinates;
use Boilerwork\Support\ValueObjects\Geo\Country\Iso31661Alpha2;
use Boilerwork\Support\ValueObjects\Language\Language;
use Boilerwork\Validation\Assert;

use function var_dump;

class Location extends ValueObject
{
    private function __construct(
        private readonly MultiLingualText $name,
        private readonly Iso31661Alpha2 $iso31661Alpha2,
        private readonly Coordinates $coordinates,
    ) {
        Assert::lazy()->tryAll()
            ->that($name)
            ->notEmpty('Name must not be empty', 'location.invalidName')
            ->verifyNow();
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

    public function names(): MultiLingualText
    {
        return $this->name;
    }

    public function nameByLanguage(string $language = Language::FALLBACK): string
    {
        return $this->name->getTextByLanguage($language);
    }

    public function name(): string
    {
        return $this->name->getDefaultText();
    }

    public function iso31661Alpha2(): Iso31661Alpha2
    {
        return $this->iso31661Alpha2;
    }

    public function coordinates(): Coordinates
    {
        return $this->coordinates;
    }

    /**
     * @return array{
     *     name: string|null,
     *     iso31661Alpha2: string,
     *     coordinates: array{latitude: float, longitude: float}
     * }
     */
    public function toArray(string $language = null): array
    {
        return [
            'name' => $language ? $this->name->getTextByLanguage($language) : $this->name->getDefaultText(),
            'iso31661Alpha2' => $this->iso31661Alpha2()->toString(),
            'coordinates' => $this->coordinates->toArray(),
        ];
    }
}
