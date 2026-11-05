<?php

require __DIR__.'/application.php';

if (parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) === '/fallback') {
    dump('http-fallback-marker');
    echo 'response-after-dump';

    return;
}

header('Content-Type: application/json');
dump(['value' => 'from-http']);
echo json_encode(['ok' => true], JSON_THROW_ON_ERROR);
