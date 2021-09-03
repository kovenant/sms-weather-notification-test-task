<?php

declare(strict_types=1);

namespace App\Service;

use App\Contract\WeatherRuleInterface;
use App\Contract\WeatherSendingRuleInterface;
use App\DTO\Message;
use App\DTO\Weather;
use App\Exception\TemperatureRuleNotFound;

/**
 * Service for building Message DTO from weather data
 */
final class WeatherSendingRuleService implements WeatherSendingRuleInterface
{
    /**
     * @var WeatherRuleInterface[]
     */
    private $rules;

    /**
     * @param WeatherRuleInterface[] $rules
     */
    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * @param Weather $weather
     * @return Message
     */
    public function getMessage(Weather $weather): Message
    {
        foreach ($this->rules as $rule) {
            if ($rule->isProcessable($weather)) {
                return $rule->buildMessage($weather);
            }
        }

        throw new TemperatureRuleNotFound('Temperature rule not found', [
            'forWeather' => $weather
        ]);
    }
}
