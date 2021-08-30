<?php

namespace Shopify\Clients\Collection;

use Shopify\Core\BaseClient;

class Client extends BaseClient
{
    public function getCollections() {
        $mutation = '{ collections (first: 50:AFTER) 
                        {
                            pageInfo{hasNextPage hasPreviousPage}  
                            edges {
                                node {
                                    descriptionHtml
                                    handle
                                    id
                                    image {
                                      altText
                                      height
                                      id
                                      originalSrc
                                      src
                                      width
                                    }
                                    seo {
                                      description
                                      title
                                    }
                                    ruleSet {
                                      rules {
                                        relation
                                        condition
                                        column
                                      }
                                    }
                                    sortOrder
                                    title
                                    updatedAt
                                } 
                                cursor
                            }
                        }
                     }';

        $result = $this->graphRequestAllPage($mutation, 'collections');

        return $result;
    }

    public function getCollectionsBak() {
//        $url = $this->baseUri . 'collection_listings.json';
//        $url = $this->baseUri . 'smart_collections.json';
        $url = $this->baseUri . 'custom_collections.json';

        $params = [
            'limit' => 1,
        ];

        $result = $this->restRequestAllPage($url, $params);

        return $result;
    }

    /**
     * @param mixed ...$options
     * @return array
     */
    public function addCustomCollection(...$options) {
        $url = $this->baseUri . 'custom_collections.json';

        $params = [];
        $this->mergeOptions($params, $options);

        $response = $this->api->rest('POST', $url, ['custom_collection' => $params]);

        return $this->getRestData($response);
    }

    /**
     * 编辑系列
     * @param int $collectionId
     * @param array $options
     * @return array
     */
    public function editCustomCollection($collectionId, ...$options) {
        $url = $this->baseUri . 'custom_collections/' . $collectionId . '.json';

        $params = [];
        $this->mergeOptions($params, $options);

        $response = $this->api->rest('PUT', $url, ['custom_collection' => $params]);

        return $this->getRestData($response);
    }

    public function getProducts($collectionId) {
        $url = $this->baseUri . 'collections/' . $collectionId . '/products.json';

        $params = [
            'limit' => 1,
        ];
        $result = $this->restRequestAllPage($url, $params);

        return $result;
    }

    /**
     * 添加产品集合关联
     * @param $collectionId
     * @param $products
     * @return array
     */
    public function addProducts($collectionId, $products) {
        $url = $this->baseUri . 'custom_collections/' . $collectionId . '.json';
        $params = [
            "custom_collection" => [
                "collects" => $products
            ]
        ];

        $response = $this->api->rest('PUT', $url, $params);

        return $this->getRestData($response);
    }


    /**
     * @param array $options
     * @return array
     */
    public function addSmartCollection($options) {
        $url = $this->baseUri . 'smart_collections.json';

        $response = $this->api->rest('POST', $url, ['smart_collection' => $options]);

        return $this->getRestData($response);
    }

    /**
     * 编辑系列
     * @param int $collectionId
     * @param array $options
     * @return array
     */
    public function editSmartCollection($collectionId, ...$options) {
        $url = $this->baseUri . 'smart_collections/' . $collectionId . '.json';

        $params = [];
        $this->mergeOptions($params, $options);

        $response = $this->api->rest('PUT', $url, ['smart_collection' => $params]);

        return $this->getRestData($response);
    }

    /**
     * 删除产品集合关联
     * @param $collectionId
     * @param $productIds
     * @return array
     */
    public function removeProducts($collectionId, $productIds) {
        $mutation = 'mutation collectionRemoveProducts($id: ID!, $productIds: [ID!]!) {
                      collectionRemoveProducts(id: $id, productIds: $productIds) {
                        job {
                          id
                        }
                        userErrors {
                          field
                          message
                        }
                      }
                    }';
        $input = [];

        $input['id'] = 'gid://shopify/Collection/' . $collectionId;
        foreach ($productIds as $productId) {
            $input['productIds'][] = 'gid://shopify/Product/' . $productId;
        }
        $response = $this->api->graph($mutation, $input);

        return $this->getGraphData($response);
    }

    /**
     * 删除系列
     * @param int $collectionId
     * @return array
     */
    public function deleteCollection($collectionId) {
        $mutation = 'mutation collectionDelete($input: CollectionDeleteInput!) {
                      collectionDelete(input: $input) {
                        deletedCollectionId
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
                'id' => 'gid://shopify/Collection/' . $collectionId
            ],
        ];
        $response = $this->api->graph($mutation, $input);

        return $this->getGraphData($response);
    }
}