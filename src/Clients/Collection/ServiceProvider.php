<?php

namespace Shopify\Clients\Collection;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface {
    public function register(Container $app)
    {
        $app['collection'] = function ($app) {
            return new Client($app);
        };
    }
}