<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/8/19 0019
 * Time: 10:53
 */
namespace Shopify\Core\Traits;

trait WithRequest {
    public function restRequestAllPage($url, $params = []) {
        $result = [
            'data' => [],
        ];

        do {
            $response = $this->api->rest('GET', $url, $params);
            $data = $this->getRestData($response);
            $data['data'] = array_merge_recursive($result['data'], $data['data']);
            $result = $data;

            if ($data['error']) {
                break;
            }

            $link = $data['next'];
            if ($link) {
                $hasNext = true;
                $params['page_info'] = $link;
            } else {
                $hasNext = false;
            }
        } while ($hasNext);

        return $result;
    }

    public function graphRequestAllPage($mutation, $pageParentKey = '') {
        $result = [
            'data' => []
        ];

        $query = str_ireplace(':AFTER', '', $mutation);
        do {
            $response = $this->api->graph($query);

            $data = $this->getGraphData($response, $pageParentKey);

            $data['data'] = array_merge_recursive($result['data'], $data['data']);
            $result = $data;

            if ($data['error']) {
                break;
            }

            $after = $data['next'];
            if ($after) {
                $hasNext = true;
                $query = str_ireplace(':AFTER', ', after: "' . $after . '"', $mutation);
            } else {
                $hasNext = false;
            }
        } while ($hasNext);

        return $result;
    }
}