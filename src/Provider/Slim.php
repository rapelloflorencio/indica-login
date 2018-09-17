<?php

declare(strict_types=1);

namespace App\Provider;

use Doctrine\ORM\EntityManager;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Slim\App;
use UMA\DoctrineDemo\Action\CreateUser;
use UMA\DoctrineDemo\Action\ListUsers;

/**
 * A ServiceProvider for registering services related
 * to Slim such as request handlers, routing and the
 * App service itself.
 */
class Slim implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $cnt)
    {
        $cnt[App::class] = function (Container $cnt): App {
            $app = new App($cnt);

            return $app;
        };
    }
}
