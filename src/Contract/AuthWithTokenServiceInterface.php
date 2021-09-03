<?php

declare(strict_types=1);

namespace App\Contract;

interface AuthWithTokenServiceInterface
{
    /**
     * @return string
     */
    public function getToken(): string;
}
