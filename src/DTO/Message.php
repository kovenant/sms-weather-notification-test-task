<?php

declare(strict_types=1);

namespace App\DTO;

/**
 * DTO for transferring required data for sending service
 */
final class Message
{
    /**
     * @var string
     */
    private $text;
    /**
     * @var string
     */
    private $recipient;

    /**
     * @param string $text
     * @param string $recipient
     */
    public function __construct(string $text, string $recipient)
    {
        $this->text = $text;
        $this->recipient = $recipient;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getRecipient(): string
    {
        return $this->recipient;
    }
}
