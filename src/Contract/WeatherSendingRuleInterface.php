<?php

declare(strict_types=1);

namespace App\Contract;

use App\DTO\Message;
use App\DTO\Weather;

interface WeatherSendingRuleInterface
{
    /**
     * @param Weather $weather
     * @return Message
     */
    public function getMessage(Weather $weather): Message;
}
