<?php
/**
 * 在其他渠道发生交易后，又想在shopify上统一计算的情况，需要用到草稿订单
 * 创建
 * 获取
 * 更新
 * 删除
 */
namespace Shopify\Clients\Order;

use Shopify\Core\BaseClient;

class DraftOrder extends BaseClient
{
    /**
     * 获取草稿订单列表，
     * @param mixed ...$options
     * @return array
     */
    public function getOrders(...$options) {
        $url = $this->baseUri . 'draft_orders.json';

        $params = [];
        $this->mergeOptions($params, $options);

        $result = $this->restRequestAllPage($url, $params);

        return $result;
    }

    /**
     * 获取指定订单
     * @param $drafOrderId
     * @return array
     */
    public function getOrder($drafOrderId) {
        $url = $this->baseUri . 'draft_orders/' . $drafOrderId . '.json';

        $response = $this->api->rest('GET', $url);

        return $this->getRestData($response);
    }

    /**
     * 添加新品
     * @param array $options
     * @return array
     */
    public function addOrder($options) {
        $url = $this->baseUri . 'draft_orders.json';

        $response = $this->api->rest('POST', $url, $options);

        return $this->getRestData($response);
    }

    /**
     * 编辑订单
     * @param int $draftOrderId
     * @param array $options
     * @return array
     */
    public function editOrder($draftOrderId, $options) {
        $url = $this->baseUri . 'draft_orders/' . $draftOrderId . '.json';

        $response = $this->api->rest('PUT', $url, $options);

        return $this->getRestData($response);
    }

    /**
     * 发送草稿订单的发票到指定邮箱
     * @param int $draftOrderId
     * @param array $options
     * @return array
     */
    public function sendInvoice($draftOrderId, $options) {
        $url = $this->baseUri . 'draft_orders/' . $draftOrderId . '/send_invoice.json';

        $response = $this->api->rest('POST', $url, $options);

        return $this->getRestData($response);
    }

    /**
     * 删除订单
     * @param int $draftOrderId
     * @return array
     */
    public function deleteOrder($draftOrderId) {
        $url = $this->baseUri . 'draft_orders/' . $draftOrderId . '.json';

        $response = $this->api->rest('DELETE', $url);

        return $this->getRestData($response);
    }

    /**
     * 完结草稿订单
     * @param $draftOrderId
     * @param $paymentPending, true标记为待处理，false默认标记为已付款
     * @return array
     */
    public function completeOrder($draftOrderId, $paymentPending = false) {
        $url = $this->baseUri . 'draft_orders/' . $draftOrderId . '/complete.json';

        if ($paymentPending) {
            $url .= '?payment_pending=true';
        }
        $response = $this->api->rest('PUT', $url);

        return $this->getRestData($response);
    }
}