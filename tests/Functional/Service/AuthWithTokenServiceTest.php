<?php

declare(strict_types=1);

namespace App\Tests\Functional\Service;

use App\Contract\AuthWithTokenServiceInterface;
use App\DI\Bootstrap;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;

class AuthWithTokenServiceTest extends TestCase
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
     * @dataProvider getTokenSuccessDataProvider
     */
    public function testGetTokenSuccess(Response $response, string $expected): void
    {
        $mockHandler = new MockHandler([$response]);
        $handlerStack = HandlerStack::create($mockHandler);

        $client = new Client(['handler' => $handlerStack]);
        $this->container->set(ClientInterface::class, $client);

        $service = $this->container->get(AuthWithTokenServiceInterface::class);
        self::assertSame($expected, $service->getToken());
    }

    /**
     * @dataProvider getTokenFailDataProvider
     */
    public function testGetTokenFail(Response $response): void
    {
        $this->expectException('App\Exception\SmsServiceAuthException');

        $mockHandler = new MockHandler([$response]);
        $handlerStack = HandlerStack::create($mockHandler);

        $client = new Client(['handler' => $handlerStack]);
        $this->container->set(ClientInterface::class, $client);

        $service = $this->container->get(AuthWithTokenServiceInterface::class);
        $service->getToken();
    }

    public function getTokenSuccessDataProvider(): array
    {
        return [
            [
                new Response(
                    200,
                    [],
                    '{"access_token":"b2014e88-86ff-44fc-ac98-ebaf24ff88cf","token_type":"bearer","expires_in":3599,"scope":"voice lookup virtual_number contact report sms 2step number_validator account failover number_pool forms transactional_email email_sender promotional_email email_validator url_analyzer","permissions":["MT_ROLE_LOOKUP","MT_ROLE_NUMBER_VALIDATOR","MT_ROLE_ACCOUNT_FINANCE","MT_ROLE_SMS","MT_ROLE_REPORT","MT_ROLE_VOICE","MT_ROLE_NUMBER_POOL","MT_ROLE_2STEP","MT_ROLE_VIRTUAL_NUMBER","MT_ROLE_CONTACT","MT_ROLE_FAILOVER","MT_ROLE_FORMS","MT_ROLE_TRANSACTIONAL_EMAIL","MT_ROLE_EMAIL_SENDER","MT_ROLE_PRICING_PACKAGES","MT_ROLE_PROMOTIONAL_EMAIL","MT_ROLE_EMAIL_VALIDATOR","MT_ROLE_URL_ANALYZER"]}'
                ),
                'b2014e88-86ff-44fc-ac98-ebaf24ff88cf'
            ],
            [
                new Response(
                    200,
                    [],
                    '{"access_token":"b2014e88-86ff-44fc-ac98-ebaf24ff88cf","token_type":"bearer","expires_in":1809,"scope":"voice lookup virtual_number contact report sms 2step number_validator account failover number_pool forms transactional_email email_sender promotional_email email_validator url_analyzer","permissions":["MT_ROLE_LOOKUP","MT_ROLE_NUMBER_VALIDATOR","MT_ROLE_ACCOUNT_FINANCE","MT_ROLE_SMS","MT_ROLE_REPORT","MT_ROLE_VOICE","MT_ROLE_NUMBER_POOL","MT_ROLE_2STEP","MT_ROLE_VIRTUAL_NUMBER","MT_ROLE_CONTACT","MT_ROLE_FAILOVER","MT_ROLE_FORMS","MT_ROLE_TRANSACTIONAL_EMAIL","MT_ROLE_EMAIL_SENDER","MT_ROLE_PRICING_PACKAGES","MT_ROLE_PROMOTIONAL_EMAIL","MT_ROLE_EMAIL_VALIDATOR","MT_ROLE_URL_ANALYZER"]}'
                ),
                'b2014e88-86ff-44fc-ac98-ebaf24ff88cf'
            ],
        ];
    }

    public function getTokenFailDataProvider(): array
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
        ];
    }
}
