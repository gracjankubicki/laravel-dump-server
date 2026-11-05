<?php

namespace BeyondCode\DumpServer\Tests;

use BeyondCode\DumpServer\DumpServerServiceProvider;
use InvalidArgumentException;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
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

    public function test_default_host_is_local_and_server_uses_it(): void
    {
        self::assertSame('tcp://127.0.0.1:9912', config('debug-server.host'));
        self::assertSame('tcp://127.0.0.1:9912', $this->app->make(DumpServer::class)->getHost());
    }

    public function test_artisan_command_is_registered(): void
    {
        $this->artisan('dump-server', ['--help' => true])->assertSuccessful();
    }

    public function test_configured_host_is_used_by_server(): void
    {
        config(['debug-server.host' => 'tcp://127.0.0.1:19912']);
        (new DumpServerServiceProvider($this->app))->register();
        self::assertSame('tcp://127.0.0.1:19912', $this->app->make(DumpServer::class)->getHost());
    }

    public static function invalidHosts(): iterable
    {
        yield 'integer' => [9912];
        yield 'null' => [null];
        yield 'array' => [[]];
        yield 'boolean' => [false];
    }

    #[DataProvider('invalidHosts')]
    public function test_invalid_host_has_clear_error(mixed $host): void
    {
        config(['debug-server.host' => $host]);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('debug-server.host configuration must be a string');
        (new DumpServerServiceProvider($this->app))->register();
    }
}
