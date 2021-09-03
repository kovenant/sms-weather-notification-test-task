<?php

declare(strict_types=1);

namespace App\Service\Output;

use App\Contract\OutputServiceInterface;

/**
 * Simple output service for testing purposes
 */
final class SimpleOutputService implements OutputServiceInterface
{
    /**
     * @param string $string
     */
    public function print(string $string): void
    {
        echo $string . PHP_EOL;
    }

    /**
     * @param string $string
     */
    public function printError(string $string): void
    {
        echo $string . PHP_EOL;
    }

    /**
     * @param string $string
     */
    public function printSuccess(string $string): void
    {
        echo $string . PHP_EOL;
    }
}
