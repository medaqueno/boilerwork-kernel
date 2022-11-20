#!/usr/bin/env php
<?php

declare(strict_types=1);

use Boilerwork\Support\ValueObjects\Url;
use Boilerwork\Validation\CustomAssertionFailedException;
use PHPUnit\Framework\TestCase;
// use Deminy\Counit\TestCase;

final class UrlTest extends TestCase
{
    public function providerUrl(): iterable
    {
        yield [
            $this->getMockForAbstractClass(
                Url::class,
                ['http://username:password@hostname:9090/path/path2?arg=value#anchor']
            )
        ];
    }

    /**
     * @test
     * @dataProvider providerUrl
     * @covers \Boilerwork\Support\ValueObjects\Url
     **/
    public function testNewUrl(Url $url): void
    {
        $this->assertInstanceOf(
            Url::class,
            $url
        );

        $this->assertEquals('http', $url->scheme(), 'Invalid scheme value');
        $this->assertEquals('username', $url->user(), 'Invalid user value');
        $this->assertEquals('password', $url->password(), 'Invalid password value');
        $this->assertEquals('hostname', $url->host(), 'Invalid host value');
        $this->assertEquals(9090, $url->port(), 'Invalid port value');
        $this->assertEquals('/path/path2', $url->path(), 'Invalid path value');
        $this->assertEquals('arg=value', $url->query(), 'Invalid query value');
        $this->assertEquals('anchor', $url->fragment(), 'Invalid fragment value');
    }
}
