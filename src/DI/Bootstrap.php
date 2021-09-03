<?php

declare(strict_types=1);

namespace App\DI;

use DI\Container;
use DI\ContainerBuilder;

final class Bootstrap
{
    /**
     * @return Container
     * @throws \Exception
     */
    public function getContainer(): Container
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions(__DIR__ . '/services.php');
        $builder->addDefinitions(__DIR__ . '/variables.php');

        $env = $_ENV['env'] ?? null;

        if ($env !== null) {
            $builder->addDefinitions(__DIR__ . '/services.' . $env . '.php');
            $builder->addDefinitions(__DIR__ . '/variables.' . $env . '.php');
        }

        return $builder->build();
    }
}
