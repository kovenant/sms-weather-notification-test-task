<?php

declare(strict_types=1);

use App\SendingRule\TemperatureBetweenRule;

return [
    'sleepTime' => 10 * 60,
    'repeatCount' => 10,
    'cityName' => 'Thessaloniki',
    'weatherApiUrl' => 'https://api.openweathermap.org/data/2.5/weather',
    'weatherApiKey' => 'b385aa7d4e568152288b3c9f5c2458a5',
    'smsServiceApiUrl' => 'https://connect.routee.net/sms',
    'smsServiceSenderName' => 'amdTelecom',
    'authServiceApiUrl' => 'https://auth.routee.net/oauth/token',
    'authServiceAppId' => '5c5d5e28e4b0bae5f4accfec',
    'authServiceAppSecret' => 'MGkNfqGud0',
    'sendingRules' => [
        new TemperatureBetweenRule(
            20,
            100,
            'Your name and Temperature more than 20C.',
            '+306911111111'
        ),
        new TemperatureBetweenRule(
            -100,
            20,
            'Your name and Temperature less than 20C.',
            '+306911111111'
        ),
    ],
];
