<?php

namespace Shopify\Clients\Order;

use Shopify\Core\BaseClient;

class Order extends BaseClient
{
    /**
     * 获取订单列表
     * @param mixed ...$options
     * @return array
     */
    public function getOrders(...$options) {
        $url = $this->baseUri . 'orders.json';

        $params = [];
        $this->mergeOptions($params, $options);

        $result = $this->restRequestAllPage($url, $params);

        return $result;
    }

    /**
     * 获取指定订单
     * @param $orderId
     * @return array
     */
    public function getOrder($orderId) {
        $url = $this->baseUri . 'orders/' . $orderId . '.json';

        $response = $this->api->rest('GET', $url);

        return $this->getRestData($response);
    }

    /**
     * 取消订单。已付款且已发货的订单无法取消。
     * 订单状态更新为取消，并且可以指定部分产品退款，不指定产品，则全部退款
     * @param $orderId
     * @param $options
     * @return array
     */
    public function cancelOrder($orderId, $options = []) {
        $url = $this->baseUri . 'orders/' . $orderId . '/cancel.json';

        $response = $this->api->rest('POST', $url, $options);

        return $this->getRestData($response);
    }

    /**
     * 关闭订单
     * 关闭的订单是没有更多工作要做的订单。所有项目都已完成或退款。
     * @param $orderId
     * @return array
     */
    public function closeOrder($orderId) {
        $url = $this->baseUri . 'orders/' . $orderId . '/close.json';

        $response = $this->api->rest('POST', $url);

        return $this->getRestData($response);
    }

    /**
     * 重新打开已关闭订单
     * @param $orderId
     * @return array
     */
    public function openOrder($orderId) {
        $url = $this->baseUri . 'orders/' . $orderId . '/open.json';

        $response = $this->api->rest('POST', $url);

        return $this->getRestData($response);
    }

    public function addOrder($options) {
        $url = $this->baseUri . 'orders.json';

        $response = $this->api->rest('POST', $url, $options);

        return $this->getRestData($response);
    }

    /**
     * 编辑订单
     * 更新订单的属性，包括
     *  buyer_accepts_marketing 买方是否接受营销
     *  email   邮箱
     *  phone   电话
     *  note    备注
     *  note_attributes 备注属性
     *  tags    标签
     *  metafields  添加元字段
     *  shipping_address    送货地址
     *  customer
     * @param int $orderId
     * @param array $options
     * @return array
     */
    public function editSimpleOrder($orderId, $options) {
        $url = $this->baseUri . 'orders/' . $orderId . '.json';

        $response = $this->api->rest('PUT', $url, ['order' => $options]);

        return $this->getRestData($response);
    }

    /**
     * 编辑订单，更新订单产品相关的开始
     * 返回calculatedOrderId，后续的相关修改都需要这个id
     * @param $orderId
     * @return array
     */
    public function editOrderBegin($orderId) {
        $mutation = 'mutation orderEditBegin($id: ID!) {
                      orderEditBegin(id: $id) {
                        calculatedOrder {
                          id
                        }
                        userErrors {
                          field
                          message
                        }
                      }
                    }';

        $input = [
            "id" => 'gid://shopify/Order/' . $orderId,
        ];

        $response = $this->api->graph($mutation, $input);

        return $this->getGraphData($response);
    }

    /**
     * 编辑订单，添加自定义产品
     * @param $calculatedOrderId
     * @param $title
     * @param $price
     * @param $quantity
     * @param bool $requiresShipping
     * @param bool $taxable
     * @return array
     */
    public function editOrderAddCustomItem($calculatedOrderId, $title, $price, $quantity, $requiresShipping = false, $taxable = true) {
        $mutation = 'mutation orderEditAddCustomItem($id: ID!, $title: String!, $price: MoneyInput!, $quantity: Int!, $requiresShipping: Boolean, $taxable: Boolean) {
                      orderEditAddCustomItem(
                        id: $id
                        title: $title
                        price: $price
                        quantity: $quantity
                      ) {
                        calculatedLineItem {
                          id
                        }
                        calculatedOrder {
                          id
                        }
                        userErrors {
                          field
                          message
                        }
                      }
                    }';

        $input = [
            "id" => 'gid://shopify/CalculatedOrder/' . $calculatedOrderId,
            "title" => $title,
            "price" => $price,
            "quantity" => $quantity,
            "requiresShipping"  => $requiresShipping,
            "taxable"  => $taxable,
        ];

        $response = $this->api->graph($mutation, $input);

        return $this->getGraphData($response);
    }

    /**
     * 编辑订单，添加变体
     * @param $calculatedOrderId
     * @param $variantId
     * @param $quantity
     * @return array
     */
    public function editOrderAddVariant($calculatedOrderId, $variantId, $quantity) {
        $mutation = 'mutation orderEditAddVariant($id: ID!, $variantId: ID!, $quantity: Int!) {
                      orderEditAddVariant(id: $id, variantId: $variantId, quantity: $quantity) {
                        calculatedLineItem {
                          id
                        }
                        calculatedOrder {
                          id
                        }
                        userErrors {
                          field
                          message
                        }
                      }
                    }';

        $input = [
            "id" => 'gid://shopify/CalculatedOrder/' . $calculatedOrderId,
            "variantId" => 'gid://shopify/ProductVariant/' . $variantId,
            "quantity" => $quantity,
        ];

        $response = $this->api->graph($mutation, $input);

        return $this->getGraphData($response);
    }

    public function editOrderAddLineItemDiscount($calculatedOrderId, $lineItemId, $discount) {
        $mutation = 'mutation orderEditAddLineItemDiscount($id: ID!, $lineItemId: ID!, $discount: OrderEditAppliedDiscountInput!) {
                      orderEditAddLineItemDiscount(
                        id: $id
                        lineItemId: $lineItemId
                        discount: $discount
                      ) {
                        addedDiscountStagedChange {
                          id
                        }
                        calculatedLineItem {
                          id
                        }
                        calculatedOrder {
                          id
                        }
                        userErrors {
                          field
                          message
                        }
                      }
                    }';

        $input = [
            "id" => 'gid://shopify/CalculatedOrder/' . $calculatedOrderId,
            "lineItemId" => 'gid://shopify/CalculatedLineItem/' . $lineItemId,
            "discount" => $discount,
        ];

        $response = $this->api->graph($mutation, $input);

        return $this->getGraphData($response);
    }

    public function editOrderRemoveLineItemDiscount($calculatedOrderId, $discountApplicationId) {
        $mutation = 'mutation orderEditRemoveLineItemDiscount($id: ID!, $discountApplicationId: ID!) {
  orderEditRemoveLineItemDiscount(
                        id: $id
                        discountApplicationId: $discountApplicationId
                      ) {
                        calculatedLineItem {
                          id
                        }
                        calculatedOrder {
                          id
                        }
                        userErrors {
                          field
                          message
                        }
                      }
                    }';

        $input = [
            "id" => 'gid://shopify/CalculatedOrder/' . $calculatedOrderId,
            "discountApplicationId" => 'gid://shopify/CalculatedDiscountApplication/' . $discountApplicationId,
        ];

        $response = $this->api->graph($mutation, $input);

        return $this->getGraphData($response);
    }

    public function editOrderSetQuantity($calculatedOrderId, $lineItemId, $quantity) {
        $mutation = 'mutation orderEditSetQuantity($id: ID!, $lineItemId: ID!, $quantity: Int!) {
                      orderEditSetQuantity(id: $id, lineItemId: $lineItemId, quantity: $quantity) {
                        calculatedLineItem {
                          id
                        }
                        calculatedOrder {
                          id
                        }
                        userErrors {
                          field
                          message
                        }
                      }
                    }';

        $input = [
            "id" => 'gid://shopify/CalculatedOrder/' . $calculatedOrderId,
            "lineItemId" => 'gid://shopify/CalculatedLineItem/' . $lineItemId,
            "quantity" => $quantity,
        ];

        $response = $this->api->graph($mutation, $input);

        return $this->getGraphData($response);
    }

    public function editOrderCommit($calculatedOrderId) {
        $mutation = 'mutation orderEditCommit($id: ID!) {
                      orderEditCommit(id: $id) {
                        order {
                          id
                        }
                        userErrors {
                          field
                          message
                        }
                      }
                    }';

        $input = [
            "id" => 'gid://shopify/CalculatedOrder/' . $calculatedOrderId,
        ];

        $response = $this->api->graph($mutation, $input);

        return $this->getGraphData($response);
    }

    public function refundOrderCalculate($orderId, $options) {
        $url = $this->baseUri . 'orders/' . $orderId . '/refunds/calculate.json';

        $response = $this->api->rest('POST', $url, ['refund' => $options]);
        return $this->getRestData($response);
    }

    public function refundOrder($orderId, $options) {
        $url = $this->baseUri . 'orders/' . $orderId . '/refunds.json';

        $response = $this->api->rest('POST', $url, ['refund' => $options]);
        return $this->getRestData($response);
    }

    /**
     * 删除订单
     * @param int $orderId
     * @return array
     */
    public function deleteOrder($orderId) {
        $url = $this->baseUri . 'orders/' . $orderId . '.json';

        $response = $this->api->rest('DELETE', $url);

        return $this->getRestData($response);
    }
}