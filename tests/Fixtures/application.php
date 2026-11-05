<?php

use BeyondCode\DumpServer\DumpServerServiceProvider;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

require dirname(__DIR__, 2).'/vendor/autoload.php';

$app = new Application(dirname(__DIR__, 2));
$app->instance('config', new Repository([
    'app' => ['env' => 'testing', 'debug' => true],
    'debug-server' => ['host' => getenv('DUMP_SERVER_HOST') ?: 'tcp://127.0.0.1:9912'],
]));
$app->instance('request', Request::capture());
$app->register(DumpServerServiceProvider::class);
$app->boot();

return $app;
