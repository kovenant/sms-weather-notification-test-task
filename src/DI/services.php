<?php

declare(strict_types=1);

use App\Contract\AuthWithTokenServiceInterface;
use App\Contract\OutputServiceInterface;
use App\Contract\SenderInterface;
use App\Contract\WeatherSendingRuleInterface;
use App\Contract\WeatherServiceInterface;
use App\Processor\WeatherNotificationProcessor;
use App\Service\AuthWithTokenService;
use App\Service\OpenWeatherServiceService;
use App\Service\Output\SymfonyOutputService;
use App\Service\SmsService;
use App\Service\WeatherSendingRuleService;
use GuzzleHttp\Client;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

return [
    WeatherNotificationProcessor::class => DI\autowire(WeatherNotificationProcessor::class)->constructor(
        DI\get('sleepTime'),
        DI\get('repeatCount'),
        DI\get('cityName')
    ),
    WeatherServiceInterface::class => DI\autowire(OpenWeatherServiceService::class)->constructor(
        DI\get('weatherApiUrl'),
        DI\get('weatherApiKey')
    ),
    SenderInterface::class => DI\autowire(SmsService::class)->constructor(
        DI\get('smsServiceApiUrl'),
        DI\get('smsServiceSenderName')
    ),
    AuthWithTokenServiceInterface::class => DI\autowire(AuthWithTokenService::class)->constructor(
        DI\get('authServiceApiUrl'),
        DI\get('authServiceAppId'),
        DI\get('authServiceAppSecret')
    ),
    WeatherSendingRuleInterface::class => DI\autowire(WeatherSendingRuleService::class)->constructor(
        DI\get('sendingRules')
    ),
    OutputServiceInterface::class => DI\get(SymfonyOutputService::class),
    ClientInterface::class => DI\get(Client::class),
    OutputInterface::class => DI\get(ConsoleOutput::class),
    LoggerInterface::class => DI\get(NullLogger::class),
];
