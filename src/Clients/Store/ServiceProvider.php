<?php

namespace Shopify\Clients\Store;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface {
    public function register(Container $app)
    {
        $app['country'] = function ($app) {
            return new Country($app);
        };

        $app['shop'] = function ($app) {
            return new Shop($app);
        };

        $app['location'] = function ($app) {
            return new Location($app);
        };
    }
}