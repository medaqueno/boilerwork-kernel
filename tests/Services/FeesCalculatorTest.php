#!/usr/bin/env php
<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Boilerwork\Support\Services\FeesCalculator\ValueObjects\Price\Price;
use Boilerwork\Support\Services\FeesCalculator\Fees\FeeForex;
use Boilerwork\Support\Services\FeesCalculator\Fees\FeeAgency;
use Boilerwork\Support\Services\FeesCalculator\FeesCalculator;
use Boilerwork\Support\Services\FeesCalculator\Fees\FeeOperator;
use Boilerwork\Support\Services\FeesCalculator\FeesCalculatorBuilder;
use Boilerwork\Support\Services\FeesCalculator\FeesCalculatorDataProvider;
use Boilerwork\Support\Services\FeesCalculator\ExchangeRates\FixedExchangeRates;

// use Deminy\Counit\TestCase;

final class FeesCalculatorTest extends TestCase
{
    public function feesCalculatorProvider(): iterable
    {
        yield "net, all fees with percent and exchange" => [
            new FeesCalculatorDataProvider(
                serviceFeeType: 'accommodation',
                tenantCurrency: 'EUR',
                operatorFees: ['accommodation' => new FeeOperator(10, 'percent'), 'transport' => new FeeOperator(0, 'percent')],
                agencyFees: ['accommodation' => new FeeAgency(20, 'percent'), 'transport' => new FeeAgency(0, 'percent')],
                forexFee: new FeeForex(3, 'percent'),
                ratesExchange: new FixedExchangeRates(0.943396226),
            )
            ,
            Price::fromData('net', 1000, 'USD'),
            1337.29
        ];
        yield "net, all fees with total and exchange" => [
            new FeesCalculatorDataProvider(
                serviceFeeType: 'accommodation',
                tenantCurrency: 'EUR',
                operatorFees: ['accommodation' => new FeeOperator(2, 'total')],
                agencyFees: ['accommodation' => new FeeAgency(150, 'total')],
                forexFee: new FeeForex(20, 'total'),
                ratesExchange: new FixedExchangeRates(0.943396226),
            ),
            Price::fromData('net', 1000, 'USD'),
            1115.4
        ];
        yield "retail with all fees with percent and exchange" => [
            new FeesCalculatorDataProvider(
                serviceFeeType: 'accommodation',
                tenantCurrency: 'EUR',
                operatorFees: ['accommodation' => new FeeOperator(10, 'percent')],
                agencyFees: ['accommodation' => new FeeAgency(20, 'percent')],
                forexFee: new FeeForex(3, 'percent'),
                ratesExchange: new FixedExchangeRates(0.943396226),
            ),
            Price::fromData('retail', 1000, 'USD'),
            972.57
        ];
        yield "retail with all fees with total and exchange" => [
            new FeesCalculatorDataProvider(
                serviceFeeType: 'accommodation',
                tenantCurrency: 'EUR',
                operatorFees: ['accommodation' => new FeeOperator(2, 'total')],
                agencyFees: ['accommodation' => new FeeAgency(150, 'total')],
                forexFee: new FeeForex(20, 'total'),
                ratesExchange: new FixedExchangeRates(0.943396226),
            ),
            Price::fromData('retail', 1000, 'USD'),
            963.4
        ];
        yield "net 0EUR, 2EUR, 0.1EUR from USD" => [
            new FeesCalculatorDataProvider(
                serviceFeeType: 'accommodation',
                tenantCurrency: 'EUR',
                operatorFees: ['accommodation' => new FeeOperator(2, 'total')],
                agencyFees: ['accommodation' => new FeeAgency(0.1, 'total')],
                forexFee: new FeeForex(0, 'total'),
                ratesExchange: new FixedExchangeRates(0.944628),
            ),
            Price::fromData('net', 1000, 'USD'),
            946.73
        ];
        yield "net 5%, 20%, 30% from USD" => [
            new FeesCalculatorDataProvider(
                serviceFeeType: 'accommodation',
                tenantCurrency: 'EUR',
                operatorFees: ['accommodation' => new FeeOperator(20, 'percent')],
                agencyFees: ['accommodation' => new FeeAgency(30, 'percent')],
                forexFee: new FeeForex(5, 'percent'),
                ratesExchange: new FixedExchangeRates(0.944628),
            ),
            Price::fromData('net', 1000, 'USD'),
            1704.59
        ];
        yield "net 1%, 1%, 0.1% from USD" => [
            new FeesCalculatorDataProvider(
                serviceFeeType: 'accommodation',
                tenantCurrency: 'EUR',
                operatorFees: ['accommodation' => new FeeOperator(1, 'percent')],
                agencyFees: ['accommodation' => new FeeAgency(0.1, 'percent')],
                forexFee: new FeeForex(1, 'percent'),
                ratesExchange: new FixedExchangeRates(0.944628),
            ),
            Price::fromData('net', 1000, 'USD'),
            964.68
        ];
        yield "net 1EUR, 1% ,0.1% from USD" => [
            new FeesCalculatorDataProvider(
                serviceFeeType: 'accommodation',
                tenantCurrency: 'EUR',
                operatorFees: ['accommodation' => new FeeOperator(1, 'percent')],
                agencyFees: ['accommodation' => new FeeAgency(0.1, 'percent')],
                forexFee: new FeeForex(1, 'total'),
                ratesExchange: new FixedExchangeRates(0.944628),
            ),
            Price::fromData('net', 1000, 'USD'),
            956.04
        ];
        yield "net 1%, 2EUR, 0.1% from USD" => [
            new FeesCalculatorDataProvider(
                serviceFeeType: 'accommodation',
                tenantCurrency: 'EUR',
                operatorFees: ['accommodation' => new FeeOperator(2, 'total')],
                agencyFees: ['accommodation' => new FeeAgency(0.1, 'percent')],
                forexFee: new FeeForex(1, 'percent'),
                ratesExchange: new FixedExchangeRates(0.944628),
            ),
            Price::fromData('net', 1000, 'USD'),
            957.13
        ];
        yield "net 1EUR, 2EUR, 0.1% from USD" => [
            new FeesCalculatorDataProvider(
                serviceFeeType: 'accommodation',
                tenantCurrency: 'EUR',
                operatorFees: ['accommodation' => new FeeOperator(2, 'total')],
                agencyFees: ['accommodation' => new FeeAgency(0.1, 'percent')],
                forexFee: new FeeForex(1, 'total'),
                ratesExchange: new FixedExchangeRates(0.944628),
            ),
            Price::fromData('net', 1000, 'USD'),
            948.58
        ];
        yield "net 1EUR, 2EUR, 0.1EUR from USD" => [
            new FeesCalculatorDataProvider(
                serviceFeeType: 'accommodation',
                tenantCurrency: 'EUR',
                operatorFees: ['accommodation' => new FeeOperator(2, 'total')],
                agencyFees: ['accommodation' => new FeeAgency(0.1, 'total')],
                forexFee: new FeeForex(1, 'total'),
                ratesExchange: new FixedExchangeRates(0.944628),
            ),
            Price::fromData('net', 1000, 'USD'),
            947.73
        ];
        yield "net 1%, 2EUR, 0.1EUR from USD" => [
            new FeesCalculatorDataProvider(
                serviceFeeType: 'accommodation',
                tenantCurrency: 'EUR',
                operatorFees: ['accommodation' => new FeeOperator(2, 'total')],
                agencyFees: ['accommodation' => new FeeAgency(0.1, 'total')],
                forexFee: new FeeForex(1, 'percent'),
                ratesExchange: new FixedExchangeRates(0.944628),
            ),
            Price::fromData('net', 1000, 'USD'),
            956.27
        ];
        yield "net 1%, 1%, 0.1EUR from USD" => [
            new FeesCalculatorDataProvider(
                serviceFeeType: 'accommodation',
                tenantCurrency: 'EUR',
                operatorFees: ['accommodation' => new FeeOperator(1, 'percent')],
                agencyFees: ['accommodation' => new FeeAgency(0.1, 'total')],
                forexFee: new FeeForex(1, 'percent'),
                ratesExchange: new FixedExchangeRates(0.944628),
            ),
            Price::fromData('net', 1000, 'USD'),
            963.81
        ];
        yield "net 3%, 0.10%, 38% from USD" => [
            new FeesCalculatorDataProvider(
                serviceFeeType: 'accommodation',
                tenantCurrency: 'EUR',
                operatorFees: ['accommodation' => new FeeOperator(0.10, 'percent')],
                agencyFees: ['accommodation' => new FeeAgency(38, 'percent')],
                forexFee: new FeeForex(3, 'percent'),
                ratesExchange: new FixedExchangeRates(0.944628),
            ),
            Price::fromData('net', 1000, 'USD'),
            1572.29
        ];
        yield "net 3%, 0.10%, 38% from EUR" => [
            new FeesCalculatorDataProvider(
                serviceFeeType: 'accommodation',
                tenantCurrency: 'EUR',
                operatorFees: ['accommodation' => new FeeOperator(0.10, 'percent')],
                agencyFees: ['accommodation' => new FeeAgency(38, 'percent')],
                forexFee: new FeeForex(3, 'percent'),
                ratesExchange: new FixedExchangeRates(1),
            ),
            Price::fromData('net', 1000, 'EUR'),
            1614.52
        ];
    }


    /**
     * @test
     * @dataProvider feesCalculatorProvider
     **/
    public function testCalculatorFees(FeesCalculatorDataProvider $feesCalculatorDataProvider, Price $price, float $expected): void
    {
        // la llamada si con datos estaticos y test
        $feeCalculator = new FeesCalculator($feesCalculatorDataProvider);
        $pvp = $feeCalculator->sale($price);
        $this->assertEquals($expected, $pvp->money()->rounded());
    }

    // public function testCalculatorFeesFromRedis(): void
    // {
    //     // la llamada si con datos estaticos y test
    //     $feeCalculator = FeesCalculator::create(
    //         serviceType: 'accommodation',
    //         idTenant: '20597683-5e4f-4016-970d-b95808d3f07d',
    //         idCart: null,
    //         servicesInCart: ['transport']
    //     );

    //     $pvp = $feeCalculator->sale(Price::fromData('retail', 1000, 'USD'));
    //     var_dump('FEE1', $pvp->fees);
    //     $this->assertEquals(103.5, $pvp->price->amount());
    // }

    // public function testCalculatorFeesFromRedis(): void
    // {
    //     // la llamada si con datos estaticos y test
    //     $feeCalculator = FeesCalculatorBuilder::fromRedis(
    //         serviceType: 'accommodation',
    //         idTenant: '39bb7b87-6fa4-47f6-97bd-0e08a5a0c821',
    //         //idCart: '988625c5-0276-7b7a-b94a-572c78bf51b5'
    //     );

    //     $pvp = $feeCalculator->sale(Price::fromData('retail', 1000, 'USD'));
    //     var_dump('FEE1', $pvp->fees);
    //     $this->assertEquals(103.5, $pvp->price->amount());
    // }
}
