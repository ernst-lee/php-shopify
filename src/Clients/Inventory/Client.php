<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/9/24 0024
 * Time: 16:58
 */

namespace Shopify\Clients\Inventory;

use Shopify\Core\BaseClient;

class Client extends BaseClient
{
    public function bulkAdjustQuantity($inventoryItemAdjustments, $locationId) {
        $mutation = 'mutation inventoryBulkAdjustQuantityAtLocation($inventoryItemAdjustments: [InventoryAdjustItemInput!]!, $locationId: ID!) {
                      inventoryBulkAdjustQuantityAtLocation(inventoryItemAdjustments: $inventoryItemAdjustments, locationId: $locationId) {
                        userErrors {
                          field
                          message
                        }
                        inventoryLevels {
                          available
                          id
                        }
                      }
                    }';

        $input = [
            "inventoryItemAdjustments" => $inventoryItemAdjustments,
            "locationId" => "gid://shopify/Location/" . $locationId,
        ];

        $response = $this->api->graph($mutation, $input);
        return $this->getGraphData($response);
    }
}