<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/8/18 0018
 * Time: 19:30
 */
namespace Shopify\Core;

use Shopify\Core\Traits\WithRequest;
use Shopify\Core\Traits\WithResponse;

class BaseClient{
    use WithResponse;
    use WithRequest;
    protected $app;
    protected $api;
    protected $baseUri = '/admin/api/';

    public function __construct(ServiceContainer $app)
    {
        $this->app = $app;
        $this->api = $app->getApi();
    }

    public function rest(string $method, string $url, array $params = []) {
        $response = $this->api->rest($method, $url, $params);

        return $this->getRestData($response);
    }

    public function graph(string $query, array $variables = []) {
        $response = $this->api->graph($query, $variables);

        return $this->getGraphData($response);
    }

    public function mergeOptions(array &$array1, $options) {
        if (is_array($options)) {
            foreach($options as $key => $value) {
                if(is_array($value)) {
                    $this->mergeOptions($array1, $value);
                } else {
                    $array1[$key] = $value;
                }
            }
        }
    }
}