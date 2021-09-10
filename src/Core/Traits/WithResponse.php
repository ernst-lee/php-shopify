<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/8/19 0019
 * Time: 10:53
 */
namespace Shopify\Core\Traits;

trait WithResponse {
    public function getGraphData($data, $pageParentKey = '') {
        $response = $data['body'];

        $container = $response ? $response->container : [];

        $next = '';
        if ($pageParentKey && isset($container['data'][$pageParentKey]['pageInfo']['hasNextPage'])) {
            $hasNext = $container['data'][$pageParentKey]['pageInfo']['hasNextPage'];
            if ($hasNext) {
                $end = end($container['data'][$pageParentKey]['edges']);
                $next = $end['cursor'];
            }
        }
        return [
            'data' => isset($container['data']) ? $container['data'] : '',
            'status' => $data['status'],
            'error' => $data['errors'],
            'next' => $next,
        ];
    }

    public function getRestData($data) {
        $response = $data['body'];
        $error = '';

        if ($data['errors']) {
            $body = [];
            $error = $data['body'];
        } else {
            $body = $response ? $response->container : [];
        }

        return [
            'data' => $body,
            'status' => $data['status'],
            'error' => $error,
            'next' => isset($data['link']['next']) ? $data['link']['next'] : '',
        ];
    }
}