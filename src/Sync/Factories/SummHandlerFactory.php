<?php

declare(strict_types=1);

namespace Sync\Factories;

use Sync\Handlers\SummHandler;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SummHandlerFactory
{
    public function __invoke(ContainerInterface $container): RequestHandlerInterface
    {
        return new SummHandler();
    }
}
