<?php

namespace Shopify\Clients\Inventory;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface {
    public function register(Container $app)
    {
        $app['inventory'] = function ($app) {
            return new Client($app);
        };
    }
}