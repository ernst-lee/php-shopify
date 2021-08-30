<?php

namespace Shopify\Clients\Store;

use Shopify\Core\BaseClient;

class Location extends BaseClient
{
    public function getLocations() {
        $query = '{
                      locations(first:10) {
                        edges {
                          node {
                            id
                            name
                            isActive 
                            address{
                                address1 
                                address2 
                                city
                                country
                                countryCode
                                phone
                                province
                                provinceCode
                                zip
                            }
                          }
                        }
                      }
                    }';

        $response = $this->api->graph($query);

        return $this->getGraphData($response);
    }
}