<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Output;

use App\Service\Output\SymfonyOutputService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;

class SymfonyOutputServiceTest extends TestCase
{
    /**
     * @dataProvider printDataProvider
     */
    public function testPrintSuccess(string $string): void
    {
        $service = $this->getService($string);
        $service->printSuccess($string);
    }

    /**
     * @dataProvider printDataProvider
     */
    public function testPrint(string $string): void
    {
        $service = $this->getService($string);
        $service->print($string);
    }

    /**
     * @dataProvider printDataProvider
     */
    public function testPrintError(string $string): void
    {
        $service = $this->getService($string);
        $service->printError($string);
    }

    public function printDataProvider(): array
    {
        return [
            ['test'],
            ['1234'],
            [''],
        ];
    }

    private function getService(string $string): SymfonyOutputService
    {
        $mock = $this->createMock(OutputInterface::class);
        $mock->expects($this->once())
            ->method('writeln')
            ->with($this->stringContains($string));

        return new SymfonyOutputService($mock);
    }
}
