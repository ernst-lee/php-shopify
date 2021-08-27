<?php

namespace Shopify\Clients\Order;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface {
    public function register(Container $app)
    {
        //发生支付的订单
        $app['order'] = function ($app) {
            return new Order($app);
        };

        //弃单
        $app['abandonedOrder'] = function ($app) {
            return new AbandonedOrder($app);
        };

        //草稿订单
        $app['draftOrder'] = function ($app) {
            return new DraftOrder($app);
        };
    }
}