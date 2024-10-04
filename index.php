<?php

require __DIR__ . '/vendor/autoload.php';

use Candelabro\Commands;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new Commands\LightCandle());
$application->add(new Commands\ListCandle());
$application->add(new Commands\ClearConfig());
$application->run();
