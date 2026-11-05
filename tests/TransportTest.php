<?php

namespace BeyondCode\DumpServer\Tests;

use BeyondCode\DumpServer\Tests\Support\ProcessTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Process\PhpProcess;
use Symfony\Component\Process\Process;

class TransportTest extends ProcessTestCase
{
    /** @return iterable<string, array{string}> */
    public static function formats(): iterable
    {
        yield 'cli' => ['cli'];
        yield 'html' => ['html'];
    }

    #[DataProvider('formats')]
    public function test_real_server_receives_cli_dump(string $format): void
    {
        $host = $this->unusedAddress();
        $server = $this->startProcess([PHP_BINARY, 'tests/Fixtures/server.php', 'dump-server', '--format='.$format], $host, 'Server listening');
        $script = '<?php require "vendor/autoload.php"; $connection = new Symfony\Component\VarDumper\Server\Connection('.var_export('tcp://'.$host, true).'); (new BeyondCode\DumpServer\Dumper($connection))->dump(["value" => "from-cli"]);';
        $client = new PhpProcess($script, dirname(__DIR__));
        $client->mustRun();
        self::assertSame('', $client->getOutput());
        $output = $this->receive($server, 'from-cli');
        self::assertStringContainsString('from-cli', $output);
        if ($format === 'html') {
            self::assertStringContainsString('<script', $output);
        }
    }

    public function test_http_dump_does_not_corrupt_json_and_includes_request_context(): void
    {
        $host = $this->unusedAddress();
        $server = $this->startProcess([PHP_BINARY, 'tests/Fixtures/server.php', 'dump-server'], $host, 'Server listening');
        $http = $this->unusedAddress();
        $this->startProcess([PHP_BINARY, '-S', $http, 'tests/Fixtures/http.php'], $host, 'Development Server');
        $response = file_get_contents('http://'.$http.'/json');
        self::assertSame('{"ok":true}', $response);
        $output = $this->receive($server, 'from-http');
        self::assertStringContainsString('GET http://'.$http.'/json', $output);
    }

    public function test_http_fallback_remains_readable_without_server(): void
    {
        $host = $this->unusedAddress();
        $http = $this->unusedAddress();
        $this->startProcess([PHP_BINARY, '-S', $http, 'tests/Fixtures/http.php'], $host, 'Development Server');
        $response = file_get_contents('http://'.$http.'/fallback');
        self::assertIsString($response);
        self::assertStringContainsString('http-fallback-marker', $response);
        self::assertStringContainsString('response-after-dump', $response);
        self::assertStringContainsString('<script', $response);
    }

    public function test_unsupported_format_fails_before_starting_server(): void
    {
        $host = $this->unusedAddress();
        $process = new Process([PHP_BINARY, 'tests/Fixtures/server.php', 'dump-server', '--format=csv'], dirname(__DIR__), ['DUMP_SERVER_HOST' => 'tcp://'.$host]);
        $process->setTimeout(5);
        $process->run();
        self::assertNotSame(0, $process->getExitCode());
        $output = $process->getOutput().$process->getErrorOutput();
        self::assertStringContainsString('Unsupported format "csv"', $output);
        self::assertStringNotContainsString('Server listening', $output);
    }

    public function test_occupied_port_fails_without_claiming_to_listen(): void
    {
        $socket = stream_socket_server('tcp://127.0.0.1:0');
        self::assertNotFalse($socket);
        try {
            $host = stream_socket_get_name($socket, false);
            self::assertIsString($host);
            $process = new Process([PHP_BINARY, 'tests/Fixtures/server.php', 'dump-server'], dirname(__DIR__), ['DUMP_SERVER_HOST' => 'tcp://'.$host]);
            $process->setTimeout(5);
            $process->run();
            self::assertNotSame(0, $process->getExitCode());
            self::assertStringNotContainsString('Server listening', $process->getOutput().$process->getErrorOutput());
        } finally {
            fclose($socket);
        }
    }
}
