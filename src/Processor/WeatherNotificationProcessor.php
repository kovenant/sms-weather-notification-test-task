<?php

declare(strict_types=1);

namespace App\Processor;

use App\Contract\OutputServiceInterface;
use App\Contract\SenderInterface;
use App\Contract\WeatherSendingRuleInterface;
use App\Contract\WeatherServiceInterface;
use Psr\Log\LoggerInterface;

/**
 * Main application class
 */
class WeatherNotificationProcessor
{
    /**
     * @var int in seconds
     */
    private $sleepTime;
    /**
     * @var int
     */
    private $repeatCount;
    /**
     * @var string
     */
    private $cityName;
    /**
     * @var WeatherServiceInterface
     */
    private $weatherService;
    /**
     * @var SenderInterface
     */
    private $senderService;
    /**
     * @var WeatherSendingRuleInterface
     */
    private $weatherRuleService;
    /**
     * @var OutputServiceInterface
     */
    private $output;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param int $sleepTime
     * @param int $repeatCount
     * @param string $cityName
     * @param WeatherServiceInterface $weatherService
     * @param SenderInterface $senderService
     * @param WeatherSendingRuleInterface $weatherRuleService
     * @param OutputServiceInterface $output
     * @param LoggerInterface $logger
     */
    public function __construct(
        int $sleepTime,
        int $repeatCount,
        string $cityName,
        WeatherServiceInterface $weatherService,
        SenderInterface $senderService,
        WeatherSendingRuleInterface $weatherRuleService,
        OutputServiceInterface $output,
        LoggerInterface $logger
    ) {
        $this->sleepTime = $sleepTime;
        $this->repeatCount = $repeatCount;
        $this->cityName = $cityName;
        $this->weatherService = $weatherService;
        $this->senderService = $senderService;
        $this->weatherRuleService = $weatherRuleService;
        $this->output = $output;
        $this->logger = $logger;
    }

    public function process(): void
    {
        $this->output->print('Started');

        $this->senderService->auth();

        for ($i = 0; $i < $this->repeatCount; $i++) {
            try {
                $weather = $this->weatherService->getWeatherByCityName($this->cityName);
                $message = $this->weatherRuleService->getMessage($weather);
                $this->senderService->send($message);

                $this->output->printSuccess('Sended ' . ($i + 1) . '/' . $this->repeatCount);
            } catch (\Exception $e) {
                $this->output->printError($e->getMessage());
                $this->logger->error($e, ['exception' => $e]);
            }

            if ($i + 1 < $this->repeatCount) {
                $this->output->print('Sleeping ' . $this->sleepTime . ' sec');

                sleep($this->sleepTime);
            }
        }

        $this->output->printSuccess('Finished');
    }
}
