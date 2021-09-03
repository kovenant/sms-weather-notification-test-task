<?php

declare(strict_types=1);

namespace App\DTO;

/**
 * DTO for transferring weather data from API
 */
final class Weather
{
    /**
     * @var float
     */
    private $temperature;

    /**
     * @param float $temperature
     */
    public function __construct(float $temperature)
    {
        $this->temperature = $temperature;
    }

    /**
     * @return float
     */
    public function getTemperature(): float
    {
        return $this->temperature;
    }
}
