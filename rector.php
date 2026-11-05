<?php

use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Switch_\ChangeSwitchToMatchRector;

return RectorConfig::configure()
    ->withPaths([__DIR__.'/src'])
    ->withRules([
        ChangeSwitchToMatchRector::class,
    ]);
