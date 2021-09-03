<?php

declare(strict_types=1);

namespace App\Service;

use App\Contract\AuthWithTokenServiceInterface;
use App\Contract\SenderInterface;
use App\DTO\Message;
use App\Exception\SmsServiceException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;

/**
 * Service for sending message via sms
 */
final class SmsService implements SenderInterface
{
    /**
     * @var string
     */
    private $apiUrl;
    /**
     * @var string
     */
    private $smsServiceSenderName;
    /**
     * @var string
     */
    private $token;
    /**
     * @var ClientInterface
     */
    private $client;
    /**
     * @var AuthWithTokenServiceInterface
     */
    private $authService;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param string $apiUrl
     * @param string $smsServiceSenderName
     * @param ClientInterface $client
     * @param AuthWithTokenServiceInterface $smsServiceAuth
     * @param LoggerInterface $logger
     */
    public function __construct(
        string $apiUrl,
        string $smsServiceSenderName,
        ClientInterface $client,
        AuthWithTokenServiceInterface $smsServiceAuth,
        LoggerInterface $logger
    ) {
        $this->apiUrl = $apiUrl;
        $this->client = $client;
        $this->authService = $smsServiceAuth;
        $this->smsServiceSenderName = $smsServiceSenderName;
        $this->logger = $logger;
    }

    /**
     *
     */
    public function auth(): void
    {
        $this->token = $this->authService->getToken();
    }

    /**
     * @param Message $message
     */
    public function send(Message $message): void
    {
        try {
            $response = $this->client->sendRequest(
                new Request(
                    'POST',
                    $this->apiUrl,
                    [
                        'authorization' => 'Bearer ' . $this->token,
                        'content-type' => 'application/json'
                    ],
                    json_encode([
                        'body' => $message->getText(),
                        'to' => $message->getRecipient(),
                        'from' => $this->smsServiceSenderName
                    ])
                )
            );
            $data = json_decode((string)$response->getBody(), true);

            if (array_key_exists('developerMessage', $data)) {
                throw new SmsServiceException($data['developerMessage'], $data);
            }
        } catch (\Throwable $e) {
            throw new SmsServiceException(
                'Error while sending SMS',
                [
                    'message' => $message,
                    'apiUrl' => $this->apiUrl,
                    'token' => $this->token,
                    'from' => $this->smsServiceSenderName,
                ],
                $e->getCode(),
                $e
            );
        }

        $this->logger->debug('Sms was successfully sent', $data ?? []);
    }
}
