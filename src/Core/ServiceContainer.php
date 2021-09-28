<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/8/18 0018
 * Time: 19:07
 */
namespace Shopify\Core;

use Osiset\BasicShopifyAPI\BasicShopifyAPI;
use Osiset\BasicShopifyAPI\Options;
use Osiset\BasicShopifyAPI\Session;
use Pimple\Container;

class ServiceContainer extends Container{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var array
     */
    protected $providers = [];

    /**
     * @var array
     */
    protected $defaultConfig = [
        'version' => '2021-10',
        'shop'  => '',
        'api_password' => '',
        'api_key'   => '',
        'timeout' => 5,
        'max_retry_attempts' => 3,
        'retry_on_status' => [429, 503, 400],

        'enable_redis' => false,
        'redis_config' => [
            'hostname' => '127.0.0.1',
            'port' => 6379,
            'auth' => '',
        ],
    ];

    /**
     * @var array
     */
    protected $userConfig = [];

    protected $options;
    protected $api;

    /**
     * ServiceContainer constructor.
     * @param array $config
     * @param array $prepends
     * @param string|null $id
     */
    public function __construct(array $config = [], array $prepends = [], string $id = null)
    {
        if (isset($config['redis_config']) && $config['redis_config']) {
            $config['enable_redis'] = true;
        }
        $this->userConfig = array_replace_recursive($this->defaultConfig, $config);

        $this->id = $id;

        $this->setOptions();
        parent::__construct($prepends);

        $this->registerProviders($this->providers);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id ? $this->id : $this->id = md5(json_encode($this->userConfig));
    }

    /**
     * @param string $id
     * @param mixed  $value
     */
    public function rebind($id, $value)
    {
        $this->offsetUnset($id);
        $this->offsetSet($id, $value);
    }

    /**
     * Magic get access.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function __get($id)
    {
        return $this->offsetGet($id);
    }

    /**
     * Magic set access.
     *
     * @param string $id
     * @param mixed  $value
     */
    public function __set($id, $value)
    {
        $this->offsetSet($id, $value);
    }

    protected function setOptions() {
        $config = $this->userConfig;

        $options = new Options();
        $options->setVersion($config['version']);
        $options->setType(true);
        $options->setApiKey($config['api_key']);
        $options->setApiPassword($config['api_password']);
        $options->setGuzzleOptions([
            'timeout' => $config['timeout'],
            'max_retry_attempts' => $config['max_retry_attempts'], // Was 2
            'retry_on_status'    => $config['retry_on_status'], // Was 439, 503, 500
        ]);

        $this->options = $options;
    }

    public function registerMiddleware($callback) {
        if (!$this->api) {
            $this->initialize();
        }

        $this->api->addMiddleware($callback);
    }

    public function initialize() {
        $config = $this->userConfig;

        if ($config['enable_redis']) {
            $redisStore = new RedisStore($config['redis_config']);
            $api = new BasicShopifyAPI($this->options, $redisStore, $redisStore);
        } else {
            $api = new BasicShopifyAPI($this->options);
        }

        $api->setSession(new Session($config['shop'], $config['api_key']));

        $this->api = $api;
    }

    public function getApi() {
        if (!$this->api) {
            $this->initialize();
        }

        return $this->api;
    }

    /**
     * @param array $providers
     */
    public function registerProviders(array $providers)
    {
        foreach ($providers as $provider) {
            parent::register(new $provider());
        }
    }
}
