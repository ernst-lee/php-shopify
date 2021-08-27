<?php

namespace Shopify\Clients\Store;

use Shopify\Core\BaseClient;

class Shop extends BaseClient
{
    public function getShop() {
        $url = $this->baseUri . 'shop.json';

        $response = $this->api->rest('GET', $url);

        return $this->getRestData($response);
    }
}