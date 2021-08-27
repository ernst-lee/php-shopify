<?php

namespace Shopify\Clients\Product;

use Shopify\Core\BaseClient;

class Client extends BaseClient
{
    /**
     * 获取产品列表
     * @param mixed ...$options
     * @return array
     */
    public function getProducts(...$options) {
        $url = $this->baseUri . 'products.json';

        $params = [];
        $this->mergeOptions($params, $options);

        $result = $this->restRequestAllPage($url, $params);

        return $result;
    }

    /**
     * 获取指定产品
     * @param $productId
     * @return array
     */
    public function getProduct($productId) {
        $url = $this->baseUri . 'products/' . $productId . '.json';

        $response = $this->api->rest('GET', $url);

        return $this->getRestData($response);
    }

    /**
     * 添加新品
     * @param array $options
     * @return array
     */
    public function addProduct($options) {
        $mutation = 'mutation productCreate($input: ProductInput!) {
                      productCreate(input: $input) {
                        product {
                          id
                          variants(first:250) {
                            edges{
                                node {
                                    id
                                    sku
                                }
                            }
                          }
                        }
                        shop {
                          id
                        }
                        userErrors {
                          field
                          message
                        }
                      }
                    }';

        $response = $this->api->graph($mutation, ['input' => $options]);

        return $this->getGraphData($response);
    }

    /**
     * 编辑产品
     * @param array $options
     * @return array
     */
    public function editProduct($options) {
        $mutation = 'mutation productUpdate($input: ProductInput!) {
                      productUpdate(input: $input) {
                        product {
                          id
                          variants(first:250) {
                            edges{
                                node {
                                    id
                                    sku
                                }
                            }
                          }
                        }
                        userErrors {
                          field
                          message
                        }
                      }
                    }';

        $response = $this->api->graph($mutation, ['input' => $options]);

        return $this->getGraphData($response);
    }

    public function deleteProductVariation($productVariationId) {
        $mutation = 'mutation productVariantDelete($id: ID!) {
                      productVariantDelete(id: $id) {
                        deletedProductVariantId
                        product {
                          id
                        }
                        userErrors {
                          field
                          message
                        }
                      }
                    }';

        $response = $this->api->graph($mutation, ['id' => 'gid://shopify/ProductVariant/' . $productVariationId]);

        return $this->getGraphData($response);
    }

    /**
     * 删除产品
     * @param int $productId
     * @return array
     */
    public function deleteProduct($productId) {
        $mutation = 'mutation productDelete($input: ProductDeleteInput!) {
                      productDelete(input: $input) {
                        deletedProductId
                        shop {
                          id
                        }
                        userErrors {
                          field
                          message
                        }
                      }
                    }';
        $input = [
            'input' => [
                "id" => 'gid://shopify/Product/' . $productId,
            ]
        ];
        $response = $this->api->graph($mutation, $input);

        return $this->getGraphData($response);
    }
}