<?php

use BeyondCode\DumpServer\DumpServerCommand;
use Illuminate\Console\Application;

$app = require __DIR__.'/application.php';
$console = new Application($app, $app['events'], 'test');
$console->resolve(DumpServerCommand::class);
exit($console->run());
