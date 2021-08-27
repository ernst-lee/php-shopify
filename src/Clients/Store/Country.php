<?php

namespace Shopify\Clients\Store;

use Shopify\Core\BaseClient;

class Country extends BaseClient
{
    public function getCountries() {
        $url = $this->baseUri . 'countries.json';
        $params = [];

        $result = $this->restRequestAllPage($url, $params);

        return $result;
    }

    public function addCountry($code) {
        $url = $this->baseUri . 'countries.json';

        $params = [
            "country" => [
                'code' => $code,
            ]
        ];
        $response = $this->api->rest('POST', $url, $params);

        return $this->getRestData($response);
    }

    /**
     * 删除国家
     * @param int $countryId
     * @return array
     */
    public function deleteCountry($countryId) {
        $url = $this->baseUri . 'countries/' . $countryId . '.json';

        $response = $this->api->rest('DELETE', $url);

        return $this->getRestData($response);
    }
}