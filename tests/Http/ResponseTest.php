#!/usr/bin/env php
<?php

declare(strict_types=1);

use Boilerwork\Container\IsolatedContainer;
use Boilerwork\Http\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

// use Deminy\Counit\TestCase;

final class ResponseTest extends TestCase
{
    public function providerResponses(): iterable
    {
        // Set pre-conditions to perform tests
        $isolatedContainer = new IsolatedContainer;
        globalContainer()->setIsolatedContainer($isolatedContainer);

        yield [
            (Response::create([]))->toJson(),
            Response::json([]),
            (Response::create())->toEmpty(),
            Response::empty(),
            (Response::create(''))->toText(),
            Response::text(),
        ];
    }
    /**
     * @test
     * @dataProvider providerResponses
     * @covers \App\Core\ExampleBoundedContext\UI\Ports\Http\Response
     **/
    public function testIsResponseInterface(ResponseInterface $response): void
    {
        $this->assertInstanceOf(
            ResponseInterface::class,
            $response
        );
    }

    /**
     * @test
     * @covers \App\Core\ExampleBoundedContext\UI\Ports\Http\Response
     **/
    public function testPayload(): void
    {
        $resp = Response::create(['foo' => 'bar']);
        $this->assertStringContainsString('{"metadata":[],"data":{"foo":"bar"}}', $resp->toJson()->getBody()->__toString());
    }

    /**
     * @test
     * @covers \App\Core\ExampleBoundedContext\UI\Ports\Http\Response
     **/
    public function testPayloadDirect(): void
    {
        $resp = Response::json(['foo' => 'bar']);
        $this->assertStringContainsString('{"metadata":[],"data":{"foo":"bar"}}', $resp->getBody()->__toString());
    }

    /**
     * @test
     * @covers \App\Core\ExampleBoundedContext\UI\Ports\Http\Response
     **/
    public function testMetadata(): void
    {
        $resp = Response::create();
        $resp->addMetadata(['metaAttr' => 'metaValue']);
        $resp->addMetadata(['metaAttr2' => 'metaValue2']);

        $metadata = $resp->metadata();

        $this->assertArrayHasKey('metaAttr', $metadata);
        $this->assertArrayHasKey('metaAttr2', $metadata);
        $this->assertStringContainsString('"metadata":{"metaAttr":"metaValue","metaAttr2":"metaValue2"}', $resp->toJson()->getBody()->__toString());
    }

    /**
     * @test
     * @covers \App\Core\ExampleBoundedContext\UI\Ports\Http\Response
     **/
    public function testStatus(): void
    {
        $resp = Response::create();
        $resp->setHttpStatus(418);

        $this->assertEquals(418, ($resp->toEmpty())->getStatusCode());
    }

    /**
     * @test
     * @covers \App\Core\ExampleBoundedContext\UI\Ports\Http\Response
     **/
    public function testStatusDirect(): void
    {
        $resp = Response::empty(204);
        $this->assertEquals(204, $resp->getStatusCode());
    }

    /**
     * @test
     * @covers \App\Core\ExampleBoundedContext\UI\Ports\Http\Response
     **/
    public function testHeaders(): void
    {
        $resp = Response::create();
        $resp->addHeader('x-header-1', 'value1');

        $headers = $resp->headers();

        $this->assertArrayHasKey('x-header-1', $headers);
        $this->assertArrayHasKey('x-header-1', ($resp->toEmpty())->getHeaders());
    }

    /**
     * @test
     * @covers \App\Core\ExampleBoundedContext\UI\Ports\Http\Response
     **/
    public function testHeadersDirect(): void
    {
        $resp = Response::empty(
            status: 204,
            headers: ['x-header-1' => 'value1']
        );

        $this->assertArrayHasKey('x-header-1', $resp->getHeaders());
    }
}
