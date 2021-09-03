<?php

declare(strict_types=1);

namespace App\Contract;

use App\DTO\Message;

interface SenderInterface
{
    public function auth(): void;

    /**
     * @param Message $message
     */
    public function send(Message $message): void;
}
