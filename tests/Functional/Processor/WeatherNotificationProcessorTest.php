<?php

declare(strict_types=1);

namespace App\Tests\Functional\Processor;

use App\Contract\AuthWithTokenServiceInterface;
use App\Contract\OutputServiceInterface;
use App\Contract\SenderInterface;
use App\Contract\WeatherServiceInterface;
use App\DI\Bootstrap;
use App\DTO\Message;
use App\DTO\Weather;
use App\Processor\WeatherNotificationProcessor;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;

class WeatherNotificationProcessorTest extends TestCase
{
    /**
     * @var \DI\Container
     */
    private $container;
    /**
     * @var int
     */
    private $repeatCount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = (new Bootstrap())->getContainer();
        $this->repeatCount = $this->container->get('repeatCount');
    }

    /**
     * @dataProvider processSuccessDataProvider
     */
    public function testProcessSuccess(Response $responseWeather, Message $expectedMessage): void
    {
        $mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($mockHandler);
        for ($i = 0; $i < $this->repeatCount; $i++) {
            $mockHandler->append($responseWeather);
        }

        $client = new Client(['handler' => $handlerStack]);
        $this->container->set(ClientInterface::class, $client);

        $mock = $this->createMock(SenderInterface::class);
        $mock->expects($this->exactly($this->repeatCount))
            ->method('send')
            ->with($this->equalTo($expectedMessage));
        $this->container->set(SenderInterface::class, $mock);

        $this->expectSuccessOutput('Finished', $this->repeatCount + 1);

        /** @var WeatherNotificationProcessor $output */
        $output = $this->container->get(WeatherNotificationProcessor::class);
        $output->process();
    }

    /**
     * @dataProvider processWeatherRequestErrorDataProvider
     */
    public function testProcessWeatherRequestError(Response $responseWeather): void
    {
        $mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($mockHandler);

        for ($i = 0; $i < $this->repeatCount; $i++) {
            $mockHandler->append($responseWeather);
        }

        $client = new Client(['handler' => $handlerStack]);
        $this->container->set(ClientInterface::class, $client);

        $mock = $this->createMock(SenderInterface::class);
        $this->container->set(SenderInterface::class, $mock);

        $this->expectErrorOutput('Error while fetching weather API', $this->repeatCount);

        /** @var WeatherNotificationProcessor $output */
        $output = $this->container->get(WeatherNotificationProcessor::class);
        $output->process();
    }

    public function testProcessBadTemperatureNotFound(): void
    {
        $service = $this->createMock(WeatherServiceInterface::class);
        $service->method('getWeatherByCityName')->willReturn(new Weather(100.00));
        $this->container->set(WeatherServiceInterface::class, $service);

        $mock = $this->createMock(AuthWithTokenServiceInterface::class);
        $this->container->set(AuthWithTokenServiceInterface::class, $mock);

        $this->expectErrorOutput('Temperature rule not found', $this->repeatCount);

        /** @var WeatherNotificationProcessor $output */
        $output = $this->container->get(WeatherNotificationProcessor::class);
        $output->process();
    }

    public function processSuccessDataProvider(): array
    {
        return [
            [
                new Response(
                    200,
                    [],
                    '{"coord":{"lon":22.9439,"lat":40.6403},"weather":[{"id":801,"main":"Clouds","description":"few clouds","icon":"02d"}],"base":"stations","main":{"temp":17.77,"feels_like":17.47,"temp_min":16.32,"temp_max":19.34,"pressure":1017,"humidity":40},"visibility":10000,"wind":{"speed":1.79,"deg":225,"gust":4.47},"clouds":{"all":20},"dt":1630676167,"sys":{"type":2,"id":2036703,"country":"GR","sunrise":1630641444,"sunset":1630688292},"timezone":10800,"id":734077,"name":"Thessaloniki","cod":200}'
                ),
                new Message('Your name and Temperature less than 20C. 17.77C', '+306911111111')
            ],
            [
                new Response(
                    200,
                    [],
                    '{"coord":{"lon":22.9439,"lat":40.6403},"weather":[{"id":801,"main":"Clouds","description":"few clouds","icon":"02d"}],"base":"stations","main":{"temp":27.65,"feels_like":27.47,"temp_min":26.32,"temp_max":29.34,"pressure":1017,"humidity":40},"visibility":10000,"wind":{"speed":1.79,"deg":225,"gust":4.47},"clouds":{"all":20},"dt":1630676167,"sys":{"type":2,"id":2036703,"country":"GR","sunrise":1630641444,"sunset":1630688292},"timezone":10800,"id":734077,"name":"Thessaloniki","cod":200}'
                ),
                new Message('Your name and Temperature more than 20C. 27.65C', '+306911111111')
            ],
        ];
    }


    public function processWeatherRequestErrorDataProvider(): array
    {
        return [
            [
                new Response(500),
            ],
            [
                new Response(502),
            ],
            [
                new Response(404),
            ],
            [
                new Response(403),
            ],
            [
                new Response(200),
            ],
            [
                new Response(
                    200,
                    [],
                    '{}'
                ),
            ],
            [
                new Response(
                    200,
                    [],
                    '{"coord":{"lon":22.9439,"lat":40.6403},"weather":[{"id":801,"main":"Clouds","description":"few clouds","icon":"02d"}],"base":"stations","main":{"feels_like":27.47,"temp_min":26.32,"temp_max":29.34,"pressure":1017,"humidity":40},"visibility":10000,"wind":{"speed":1.79,"deg":225,"gust":4.47},"clouds":{"all":20},"dt":1630676167,"sys":{"type":2,"id":2036703,"country":"GR","sunrise":1630641444,"sunset":1630688292},"timezone":10800,"id":734077,"name":"Thessaloniki","cod":200}'
                ),
            ],
            [
                new Response(
                    200,
                    [],
                    '{"coord":{"lon":22.9439,"lat":40.6403},"weather":[{"id":801,"main":"Clouds","description":"few clouds","icon":"02d"}],"base":"stations","main":{"temp":null,"feels_like":27.47,"temp_min":26.32,"temp_max":29.34,"pressure":1017,"humidity":40},"visibility":10000,"wind":{"speed":1.79,"deg":225,"gust":4.47},"clouds":{"all":20},"dt":1630676167,"sys":{"type":2,"id":2036703,"country":"GR","sunrise":1630641444,"sunset":1630688292},"timezone":10800,"id":734077,"name":"Thessaloniki","cod":200}'
                ),
            ],
        ];
    }

    private function expectErrorOutput(string $message, int $count): void
    {
        $this->expectOutput($count, $message, 'printError');
    }

    private function expectSuccessOutput(string $message, int $count): void
    {
        $this->expectOutput($count, $message, 'printSuccess');
    }

    private function expectOutput(int $count, string $message, string $method): void
    {
        $mock = $this->createMock(OutputServiceInterface::class);
        $mock->expects($this->exactly($count))
            ->method($method)
            ->with($this->equalTo($message));
        $this->container->set(OutputServiceInterface::class, $mock);
    }
}
