<?php

declare(strict_types=1);

namespace App\Contract;

use App\DTO\Weather;

interface WeatherServiceInterface
{
    /**
     * @param string $cityName
     * @return Weather
     */
    public function getWeatherByCityName(string $cityName): Weather;
}
