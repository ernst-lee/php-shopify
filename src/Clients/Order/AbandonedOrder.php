<?php

namespace Shopify\Clients\Order;

use Shopify\Core\BaseClient;

class AbandonedOrder extends BaseClient
{
    /**
     * 获取弃单列表,即顾客未付款订单
     * @param mixed ...$options
     * @return array
     */
    public function getOrders(...$options) {
        $url = $this->baseUri . 'checkouts.json';

        $params = [];
        $this->mergeOptions($params, $options);

        $result = $this->restRequestAllPage($url, $params);

        return $result;
    }
}