<?php

declare(strict_types=1);

use App\SendingRule\TemperatureBetweenRule;

return [
    'sleepTime' => 0,
    'repeatCount' => 10,
    'cityName' => 'Thessaloniki',
    'weatherApiUrl' => '',
    'weatherApiKey' => '',
    'smsServiceApiUrl' => '',
    'smsServiceSenderName' => '',
    'authServiceApiUrl' => '',
    'authServiceAppId' => '',
    'authServiceAppSecret' => '',
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
