<?php

namespace BeyondCode\DumpServer\Tests;

use BeyondCode\DumpServer\DumpServerServiceProvider;
use Orchestra\Testbench\TestCase;
use Symfony\Component\VarDumper\Server\DumpServer;
use Symfony\Component\VarDumper\VarDumper;

class ServiceProviderTest extends TestCase
{
    private mixed $previousHandler = null;

    protected function setUp(): void
    {
        $this->previousHandler = VarDumper::setHandler(null);
        parent::setUp();
    }

    protected function tearDown(): void
    {
        try {
            parent::tearDown();
        } finally {
            VarDumper::setHandler($this->previousHandler);
        }
    }

    protected function getPackageProviders($app): array
    {
        return [DumpServerServiceProvider::class];
    }

    public function testDefaultHostIsLocalAndServerUsesIt(): void
    {
        self::assertSame('tcp://127.0.0.1:9912', config('debug-server.host'));
        self::assertSame('tcp://127.0.0.1:9912', $this->app->make(DumpServer::class)->getHost());
    }

    public function testArtisanCommandIsRegistered(): void
    {
        $this->artisan('dump-server', ['--help' => true])->assertSuccessful();
    }
}
