<?php

declare(strict_types=1);

namespace App\Contract;

use App\DTO\Message;
use App\DTO\Weather;

interface WeatherRuleInterface
{
    /**
     * @param Weather $weather
     * @return bool
     */
    public function isProcessable(Weather $weather): bool;

    /**
     * @param Weather $weather
     * @return Message
     */
    public function buildMessage(Weather $weather): Message;
}
