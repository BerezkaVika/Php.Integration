<?php

declare(strict_types=1);

namespace Sync;

use Sync\Factories\ApiHandlerFactory;
use Sync\Handlers\ApiHandler;
use Sync\Factories\ContactsHandlerFactory;
use Sync\Handlers\ContactsHandler;

class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),

        ];
    }

/**
     * Returns the container dependencies
     */
    public function getDependencies(): array
    {
        return [
            'invokables' => [

            ],
            'factories'  => [
                Handlers\ApiHandler::class => Factories\ApiHandlerFactory::class,
            //    Handlers\ContactsHandler::class => Factories\ContactsHandlerFactory::class
            ],
        ];
    }
}
