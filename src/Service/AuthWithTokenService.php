<?php

declare(strict_types=1);

namespace App\Service;

use App\Contract\AuthWithTokenServiceInterface;
use App\Exception\SmsServiceAuthException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface;

/**
 * Service for getting authentication token
 */
final class AuthWithTokenService implements AuthWithTokenServiceInterface
{
    /**
     * @var string
     */
    private $apiAuthUrl;
    /**
     * @var string
     */
    private $appId;
    /**
     * @var string
     */
    private $appSecret;
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @param string $apiAuthUrl
     * @param string $appId
     * @param string $appSecret
     * @param ClientInterface $client
     */
    public function __construct(
        string $apiAuthUrl,
        string $appId,
        string $appSecret,
        ClientInterface $client
    ) {
        $this->apiAuthUrl = $apiAuthUrl;
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->client = $client;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        try {
            $response = $this->client->sendRequest(
                new Request(
                    'POST',
                    $this->apiAuthUrl,
                    [
                        'authorization' => 'Basic ' . $this->getBasicAuthToken(),
                        'content-type' => 'application/x-www-form-urlencoded'
                    ],
                    http_build_query([
                        'grant_type' => 'client_credentials'
                    ])
                )
            );
            $data = json_decode((string)$response->getBody(), true);
            $token = $data['access_token'] ?? null;

            if (empty($token)) {
                throw new SmsServiceAuthException('Token is empty');
            }
        } catch (\Throwable $e) {
            throw new SmsServiceAuthException(
                'Error while working with SMS Service Auth API',
                [
                    'apiAuthUrl' => $this->apiAuthUrl,
                    'appId' => $this->appId,
                    'appSecret' => $this->appSecret,
                ],
                $e->getCode(),
                $e
            );
        }

        return $token;
    }

    /**
     * @return string
     */
    private function getBasicAuthToken(): string
    {
        return base64_encode($this->appId . ':' . $this->appSecret);
    }
}
