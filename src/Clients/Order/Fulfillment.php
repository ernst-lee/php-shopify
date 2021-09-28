<?php
/**
 * 订单配送履行
 * 创建
 * 获取
 * 更新
 * 删除
 */
namespace Shopify\Clients\Order;

use Shopify\Core\BaseClient;

class Fulfillment extends BaseClient
{
    /**
     * 配送订单产品
     * @param $orderId
     * @param $locationId
     * @param $trackingParams
     * @param $lineItems
     * @param $notifyCustomer
     * @return array
     */
    public function create($orderId, $locationId, $trackingParams, $lineItems, $notifyCustomer) {
        $url = $this->baseUri . 'orders/' . $orderId . '/fulfillments.json';

        $fulfillment = [
            'location_id' => $locationId,
        ];

        foreach ($trackingParams as $key => $value) {
            if (in_array($key, ['tracking_number', 'tracking_company', 'tracking_url'])) {
                $fulfillment[$key] = $value;
            }
        }
        $fulfillment['line_items'] = $lineItems;

        if ($notifyCustomer) {
            $fulfillment['notify_customer'] = true;
        }

        $response = $this->api->rest('POST', $url, ['fulfillment' => $fulfillment]);

        return $this->getRestData($response);
    }
}