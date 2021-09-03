<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\DTO\Weather;
use App\SendingRule\TemperatureBetweenRule;
use App\Service\WeatherSendingRuleService;
use PHPUnit\Framework\TestCase;

class WeatherSendingRuleServiceTest extends TestCase
{
    /**
     * @dataProvider getMessageSuccessDataProvider
     */
    public function testGetMessageSuccess(float $temperature, string $expectedText, string $expectedRecipient): void
    {
        $ruleService = new WeatherSendingRuleService([
            new TemperatureBetweenRule(
                20,
                30,
                'Your name and Temperature more than 20C.',
                '+306911111111'
            ),
            new TemperatureBetweenRule(
                30,
                100,
                'Your name and Temperature more than 30C.',
                '+306911111113'
            ),
            new TemperatureBetweenRule(
                -100,
                20,
                'Your name and Temperature less than 20C.',
                '+306911111112'
            ),
        ]);

        $message = $ruleService->getMessage(new Weather($temperature));

        self::assertSame($expectedText, $message->getText());
        self::assertSame($expectedRecipient, $message->getRecipient());
    }

    /**
     * @dataProvider getMessageRuleExceptionDataProvider
     */
    public function testGetMessageRuleException(float $temperature): void
    {
        $ruleService = new WeatherSendingRuleService([
            new TemperatureBetweenRule(
                20,
                30,
                'Your name and Temperature more than 20C.',
                '+306911111111'
            ),
            new TemperatureBetweenRule(
                10,
                20,
                'Your name and Temperature less than 20C.',
                '+306911111112'
            ),
        ]);

        $this->expectException('App\Exception\TemperatureRuleNotFound');

        $ruleService->getMessage(new Weather($temperature));
    }

    public function getMessageSuccessDataProvider(): array
    {
        return [
            [30.01, 'Your name and Temperature more than 30C. 30.01C', '+306911111113'],
            [30, 'Your name and Temperature more than 30C. 30C', '+306911111113'],
            [30.00, 'Your name and Temperature more than 30C. 30C', '+306911111113'],
            [29.99, 'Your name and Temperature more than 20C. 29.99C', '+306911111111'],
            [21.99, 'Your name and Temperature more than 20C. 21.99C', '+306911111111'],
            [21, 'Your name and Temperature more than 20C. 21C', '+306911111111'],
            [20, 'Your name and Temperature more than 20C. 20C', '+306911111111'],
            [20.01, 'Your name and Temperature more than 20C. 20.01C', '+306911111111'],
            [20.00, 'Your name and Temperature more than 20C. 20C', '+306911111111'],
            [19.99, 'Your name and Temperature less than 20C. 19.99C', '+306911111112'],
            [19, 'Your name and Temperature less than 20C. 19C', '+306911111112'],
        ];
    }

    public function getMessageRuleExceptionDataProvider(): array
    {
        return [
            [31],
            [30],
            [30.00],
            [9.99],
            [5]
        ];
    }
}
