#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\FeesCalculator\Fees;

use Boilerwork\Support\Services\FeesCalculator\ValueObjects\Fee;


//TODO: clase abstracta FeeConFormulas o AbstractFee
final class FeeForex extends Fee implements FeeInterface
{
    const DESCRIPTION = 'forex';

    private float $exchange;
    public function __construct(float $value, string $type, float $exchange = 1)
    {
        parent::__construct($value, $type);
        $this->exchange = $exchange;
    }

    public function exchange(): float
    {
        return $this->exchange;
    }

    public function toDTO(): FeeDTO
    {
        return new FeeDTO(
            fee: $this::DESCRIPTION,
            amount: $this->value(),
            type: $this->type(),
            exchange: $this->exchange()
        );
    }

    public function percent(float $val): float
    {
        if ($this->exchange() == 1){
            return $val;
        }
        //=B10*(D14/(1 - (C19/100)))
        bcscale(10);
        /**
         * @param NumericString $val
         */
        $res = bcmul(
            (string) $val, // B10
            bcdiv(
                (string) $this->exchange(), //C14
                bcsub(
                    '1',
                    bcdiv(
                        (string) $this->value(),
                        '100'
                    )
                )
            )
        );
        // var_dump('d22', $res);
        return (float) $res;
    }

    public function total(float $val): float
    {
        if ($this->exchange() == 1){
            return $val;
        }
        //TODO: meter en la clase abstracta
        bcscale(10);
        $res = bcadd(
            bcmul(
                (string) $val,
                (string) $this->exchange()
            ),
            (string) $this->value()
        );

        // var_dump('RESSSS', $res);
        return (float) $res;
    }
}
