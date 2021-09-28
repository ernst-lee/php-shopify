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
                           images(first: 50) {
                              edges {
                                node {
                                  id
                                  src
                                }
                              }
                            }
                            variants(first: 250) {
                              edges {
                                node {
                                  image {
                                    id
                                  }
                                  id
                                  barcode
                                  compareAtPrice
                                  price
                                  sku
                                  title
                                  inventoryQuantity
                                  selectedOptions {
                                    name
                                    value
                                  }
                                }
                              }
                            }
                            createdAt
                            options {
                              name
                              values
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
                            images(first: 50) {
                              edges {
                                node {
                                  id
                                  src
                                }
                              }
                            }
                            variants(first: 250) {
                              edges {
                                node {
                                  image {
                                    id
                                  }
                                  id
                                  barcode
                                  compareAtPrice
                                  price
                                  sku
                                  title
                                  inventoryQuantity
                                  selectedOptions {
                                    name
                                    value
                                  }
                                }
                              }
                            }
                            createdAt
                            options {
                              name
                              values
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

    public function createMedia($productId, $imageSrc) {
        $mutation = 'mutation productCreateMedia($productId: ID!, $media: [CreateMediaInput!]!) {
                      productCreateMedia(productId: $productId, media: $media) {
                         media {
                            mediaContentType
                            mediaErrors {
                                code
                                details 
                                message 
                            }
                            preview {
                                image {
                                    src
                                }
                                status
                            }
                            status 
                        }
                        mediaUserErrors {
                          code
                          field
                          message
                        }
                        product {
                          id
                        }
                      }
                    }';

        $input = [
            'productId' => 'gid://shopify/Product/' . $productId,
            'media' => [
                'originalSource' => $imageSrc,
                'mediaContentType' => 'IMAGE',
            ]
        ];

        $response = $this->api->graph($mutation, $input);
        return $this->getGraphData($response);
    }

    public function appendProductImages($productId, $images) {
        $mutation = 'mutation productAppendImages($input: ProductAppendImagesInput!) {
                        productAppendImages(input: $input) {
                            newImages {
                                id
                                originalSrc 
                            }
                            product {
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
                'id' => 'gid://shopify/Product/' . $productId,
                'images' => $images,
            ]
        ];

        $response = $this->api->graph($mutation, $input);
        return $this->getGraphData($response);
    }

    public function bulkCreateProductVariants($productId, $variants) {
        $mutation = 'mutation productVariantsBulkCreate($variants: [ProductVariantsBulkInput!]!, $productId: ID!) {
                      productVariantsBulkCreate(variants: $variants, productId: $productId) {
                        product {
                          id
                        }
                        productVariants {
                          image {
                            id
                          }
                          id
                          barcode
                          compareAtPrice
                          price
                          sku
                          title
                          inventoryQuantity
                          selectedOptions {
                            name
                            value
                          }
                        }
                        userErrors {
                          code
                          field
                          message
                        }
                      }
                    }';

        $input = [
            'variants' => $variants,
            'productId' => 'gid://shopify/Product/' . $productId,
        ];

        $response = $this->api->graph($mutation, $input);
        return $this->getGraphData($response);
    }

    public function bulkUpdateProductVariants($productId, $variants) {
        $mutation = 'mutation productVariantsBulkUpdate($productId: ID!, $variants: [ProductVariantsBulkInput!]!) {
                        productVariantsBulkUpdate(productId: $productId, variants: $variants) {
                            userErrors {
                                field
                                message
                            }
                            product {
                                id
                            }
                            productVariants {
                              image {
                                id
                              }
                              id
                              barcode
                              compareAtPrice
                              price
                              sku
                              title
                              inventoryQuantity
                              selectedOptions {
                                name
                                value
                              }
                            }
                        }
                    }';

        $input = [
            'variants' => $variants,
            'productId' => 'gid://shopify/Product/' . $productId,
        ];

        $response = $this->api->graph($mutation, $input);
        return $this->getGraphData($response);
    }

    public function editProductVariant($variantId, $options) {
        $url = $this->baseUri . 'variants/' . $variantId . '.json';

        $input = [
            'id' => $variantId,
        ];
        $input = array_merge($input, $options);
        $response = $this->api->rest('PUT', $url, ['variant' => $input]);

        return $this->getRestData($response);
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