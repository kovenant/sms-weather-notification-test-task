<?php

declare(strict_types=1);

namespace App\SendingRule;

use App\Contract\WeatherRuleInterface;
use App\DTO\Message;
use App\DTO\Weather;

/**
 * Rule based on comparison of temperature ranges
 */
final class TemperatureBetweenRule implements WeatherRuleInterface
{
    /**
     * @var float
     */
    private $minTemperature;
    /**
     * @var float
     */
    private $maxTemperature;
    /**
     * @var string
     */
    private $text;
    /**
     * @var string
     */
    private $recipient;

    /**
     * @param float $minTemperature
     * @param float $maxTemperature
     * @param string $message
     * @param string $recipient
     */
    public function __construct(float $minTemperature, float $maxTemperature, string $message, string $recipient)
    {
        $this->minTemperature = $minTemperature;
        $this->maxTemperature = $maxTemperature;
        $this->text = $message;
        $this->recipient = $recipient;
    }

    /**
     * @return float
     */
    public function getMinTemperature(): float
    {
        return $this->minTemperature;
    }

    /**
     * @return float
     */
    public function getMaxTemperature(): float
    {
        return $this->maxTemperature;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getRecipient(): string
    {
        return $this->recipient;
    }

    /**
     * @param Weather $weather
     * @return bool
     */
    public function isProcessable(Weather $weather): bool
    {
        $temperature = $weather->getTemperature();

        return $temperature >= $this->getMinTemperature() && $temperature < $this->getMaxTemperature();
    }

    /**
     * @param Weather $weather
     * @return Message
     */
    public function buildMessage(Weather $weather): Message
    {
        return new Message($this->getText() . ' ' . $weather->getTemperature() . 'C', $this->getRecipient());
    }
}
