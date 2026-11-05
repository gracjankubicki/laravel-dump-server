<?php

namespace BeyondCode\DumpServer\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\PhpProcess;

class DumperTest extends TestCase
{
    public function test_null_connection_falls_back_to_cli_output(): void
    {
        $process = new PhpProcess('<?php require "vendor/autoload.php"; (new BeyondCode\DumpServer\Dumper)->dump(["nested" => ["value" => "local-marker"]]);', dirname(__DIR__));
        $process->mustRun();
        self::assertStringContainsString('local-marker', $process->getOutput());
    }

    public function test_closed_port_falls_back_to_cli_output(): void
    {
        $socket = stream_socket_server('tcp://127.0.0.1:0');
        self::assertNotFalse($socket);
        $host = stream_socket_get_name($socket, false);
        fclose($socket);
        self::assertIsString($host);
        $script = '<?php require "vendor/autoload.php"; $connection = new Symfony\Component\VarDumper\Server\Connection('.var_export('tcp://'.$host, true).'); (new BeyondCode\DumpServer\Dumper($connection))->dump("closed-port-marker");';
        $process = new PhpProcess($script, dirname(__DIR__));
        $process->mustRun();
        self::assertStringContainsString('closed-port-marker', $process->getOutput());
    }
}
