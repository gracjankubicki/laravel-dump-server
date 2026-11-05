<?php

namespace BeyondCode\DumpServer\Tests;

use BeyondCode\DumpServer\RequestContextProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\PhpProcess;

class ExtensionCompatibilityTest extends TestCase
{
    public function test_existing_untyped_overrides_remain_compatible(): void
    {
        $process = new PhpProcess(<<<'CODE'
<?php
require 'vendor/autoload.php';
$dumper = new class extends BeyondCode\DumpServer\Dumper {
    public function dump($value) { echo $value; }
};
$provider = new class(new Illuminate\Foundation\Application) extends BeyondCode\DumpServer\DumpServerServiceProvider {
    public function register() {}
    public function boot() {}
};
$command = new class(new Symfony\Component\VarDumper\Server\DumpServer('tcp://127.0.0.1:9912')) extends BeyondCode\DumpServer\DumpServerCommand {
    protected $server;
    public function handle() { return 0; }
};
if ($command->handle() !== 0) { throw new RuntimeException('Override failed'); }
$dumper->dump('compatible-overrides');
CODE, dirname(__DIR__));
        $process->mustRun();
        self::assertSame('compatible-overrides', $process->getOutput());
    }

    public function test_dumper_subclass_can_skip_parent_constructor(): void
    {
        $process = new PhpProcess(<<<'CODE'
<?php
require 'vendor/autoload.php';
$dumper = new class extends BeyondCode\DumpServer\Dumper {
    public function __construct() {}
};
$dumper->dump('subclass-fallback');
CODE, dirname(__DIR__));
        $process->mustRun();
        self::assertStringContainsString('subclass-fallback', $process->getOutput());
    }

    public function test_context_subclass_can_skip_parent_constructor(): void
    {
        $context = new class extends RequestContextProvider
        {
            public function __construct() {}
        };
        self::assertNull($context->getContext());
    }
}
