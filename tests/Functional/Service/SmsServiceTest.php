<?php

declare(strict_types=1);

namespace App\Tests\Functional\Service;

use App\Contract\SenderInterface;
use App\DI\Bootstrap;
use App\DTO\Message;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;

class SmsServiceTest extends TestCase
{
    /**
     * @var \DI\Container
     */
    private $container;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = (new Bootstrap())->getContainer();
    }

    /**
     * @dataProvider getSendSuccessDataProvider
     */
    public function testSendSuccess(Response $response, array $data): void
    {
        $mockHandler = new MockHandler([$response]);
        $handlerStack = HandlerStack::create($mockHandler);

        $client = new Client(['handler' => $handlerStack]);
        $this->container->set(ClientInterface::class, $client);

        $mock = $this->createMock(LoggerInterface::class);
        $mock->expects($this->once())
            ->method('debug')
            ->with('Sms was successfully sent', $data);
        $this->container->set(LoggerInterface::class, $mock);

        $service = $this->container->get(SenderInterface::class);
        $service->send(new Message('text', '+306911111111'));
    }

    /**
     * @dataProvider getSendFailDataProvider
     */
    public function testSendFail(Response $response): void
    {
        $this->expectException('App\Exception\SmsServiceException');

        $mockHandler = new MockHandler([$response]);
        $handlerStack = HandlerStack::create($mockHandler);

        $client = new Client(['handler' => $handlerStack]);
        $this->container->set(ClientInterface::class, $client);

        $service = $this->container->get(SenderInterface::class);
        $service->send(new Message('text', '+306911111111'));
    }

    public function getSendSuccessDataProvider(): array
    {
        $responses = [
            '{"trackingId":"64fc2d64-fed3-42eb-8967-8b42103000fd","status":"Queued","createdAt":"2021-09-04T19:55:31.797Z","from":"amdTelecom","to":"+306911111111","body":"test","bodyAnalysis":{"parts":1,"unicode":false,"characters":4},"flash":false}',
            '{"trackingId":"a8ac9f1f-459e-4e48-99de-b00a331bb60b","status":"Queued","createdAt":"2021-09-04T19:58:59.214Z","from":"amdTelecom","to":"+306911111111","body":"test2","bodyAnalysis":{"parts":1,"unicode":false,"characters":5},"flash":false}'
        ];

        $data = [];

        foreach ($responses as $response) {
            $data[] = [
                new Response(
                    200,
                    [],
                    $response
                ),
                json_decode($response, true)
            ];
        }

        return $data;
    }

    public function getSendFailDataProvider(): array
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
        ];
    }
}
