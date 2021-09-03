<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Output;

use App\Service\Output\SimpleOutputService;
use PHPUnit\Framework\TestCase;

class SimpleOutputServiceTest extends TestCase
{
    /**
     * @var SimpleOutputService
     */
    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SimpleOutputService();
    }

    /**
     * @dataProvider printDataProvider
     */
    public function testPrintSuccess(string $string): void
    {
        $this->expectOutputString($string . PHP_EOL);
        $this->service->printSuccess($string);
    }

    /**
     * @dataProvider printDataProvider
     */
    public function testPrint(string $string): void
    {
        $this->expectOutputString($string . PHP_EOL);
        $this->service->print($string);
    }

    /**
     * @dataProvider printDataProvider
     */
    public function testPrintError(string $string): void
    {
        $this->expectOutputString($string . PHP_EOL);
        $this->service->printError($string);
    }

    public function printDataProvider(): array
    {
        return [
            ['test'],
            ['1234'],
            [''],
        ];
    }
}
