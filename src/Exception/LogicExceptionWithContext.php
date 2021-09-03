<?php

declare(strict_types=1);

namespace App\Exception;

use Throwable;

/**
 * Custom exception with context. For example for logging purpose
 */
abstract class LogicExceptionWithContext extends \LogicException
{
    /**
     * @var array
     */
    protected $context;

    /**
     * @param string $message
     * @param array $context
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = '', array $context = [], $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }
}
