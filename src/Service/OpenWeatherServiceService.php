<?php

declare(strict_types=1);

namespace App\Service;

use App\Contract\WeatherServiceInterface;
use App\DTO\Weather;
use App\Exception\WeatherServiceException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface;

/**
 * Service for getting weather from OpenWeatherAPi
 */
final class OpenWeatherServiceService implements WeatherServiceInterface
{
    /**
     * @var string
     */
    private $apiUrl;
    /**
     * @var string
     */
    private $apiKey;
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @param string $apiUrl
     * @param string $apiKey
     * @param ClientInterface $client
     */
    public function __construct(
        string $apiUrl,
        string $apiKey,
        ClientInterface $client
    ) {
        $this->apiUrl = $apiUrl;
        $this->apiKey = $apiKey;
        $this->client = $client;
    }

    /**
     * @param string $cityName
     * @return Weather
     */
    public function getWeatherByCityName(string $cityName): Weather
    {
        return $this->getWeather($this->getUrlByCityName($cityName));
    }

    /**
     * @param string $cityName
     * @return string
     */
    private function getUrlByCityName(string $cityName): string
    {
        return $this->apiUrl . '?' . http_build_query([
                'q' => $cityName,
                'appid' => $this->apiKey,
                'units' => 'metric'
            ]);
    }

    /**
     * @param string $urlByCityName
     * @return Weather
     */
    private function getWeather(string $urlByCityName): Weather
    {
        try {
            $response = $this->client->sendRequest(new Request('GET', $urlByCityName));
            $data = json_decode((string)$response->getBody(), true);
            $weather = new Weather($data['main']['temp']);
        } catch (\Throwable $e) {
            throw new WeatherServiceException(
                'Error while fetching weather API',
                [
                    'url' => $urlByCityName,
                    'data' => $data ?? [],
                ],
                $e->getCode(),
                $e
            );
        }

        return $weather;
    }
}
