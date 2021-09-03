<?php

declare(strict_types=1);

namespace App\Contract;

interface OutputServiceInterface
{
    /**
     * @param string $string
     */
    public function print(string $string): void;

    /**
     * @param string $string
     */
    public function printError(string $string): void;

    /**
     * @param string $string
     */
    public function printSuccess(string $string): void;
}
