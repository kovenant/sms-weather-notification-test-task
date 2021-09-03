<?php

declare(strict_types=1);

use App\Contract\OutputServiceInterface;
use App\Service\Output\SimpleOutputService;

return [
    OutputServiceInterface::class => DI\get(SimpleOutputService::class),
];
