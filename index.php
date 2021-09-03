<?php

declare(strict_types=1);

use App\DI\Bootstrap;
use App\Processor\WeatherNotificationProcessor;

require_once __DIR__ . '/vendor/autoload.php';

try {
    $container = (new Bootstrap())->getContainer();
    $output = $container->get(WeatherNotificationProcessor::class);
    $output->process();
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}
