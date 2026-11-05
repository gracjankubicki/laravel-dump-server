<?php

namespace BeyondCode\DumpServer\Tests\Support;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

abstract class ProcessTestCase extends TestCase
{
    /** @var list<Process> */
    private array $processes = [];

    protected function tearDown(): void
    {
        foreach (array_reverse($this->processes) as $process) {
            $process->stop(0.2);
        }

        parent::tearDown();
    }

    protected function unusedAddress(): string
    {
        $socket = stream_socket_server('tcp://127.0.0.1:0');
        self::assertNotFalse($socket);
        $address = stream_socket_get_name($socket, false);
        fclose($socket);
        self::assertIsString($address);

        return $address;
    }

    /** @param list<string> $command */
    protected function startProcess(array $command, string $host, string $ready): Process
    {
        $process = new Process($command, dirname(__DIR__, 2), ['DUMP_SERVER_HOST' => 'tcp://'.$host]);
        $process->setTimeout(10);
        $this->processes[] = $process;
        $process->start();
        $started = $process->waitUntil(static fn () => str_contains($process->getErrorOutput().$process->getOutput(), $ready));
        self::assertTrue($started, $process->getErrorOutput());

        return $process;
    }

    protected function receive(Process $server, string $marker): string
    {
        $deadline = microtime(true) + 5;
        do {
            $output = $server->getOutput();
            if (str_contains($output, $marker)) {
                return $output;
            }
            usleep(10000);
        } while ($server->isRunning() && microtime(true) < $deadline);

        self::fail('Dump not received: '.$server->getErrorOutput().' '.$server->getOutput());
    }
}
