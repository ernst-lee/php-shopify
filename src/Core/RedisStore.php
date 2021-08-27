<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/8/18 0018
 * Time: 17:57
 */
namespace Shopify\Core;

use Osiset\BasicShopifyAPI\Contracts\StateStorage;
use Osiset\BasicShopifyAPI\Session;

class RedisStore implements StateStorage {
    protected $redis = null;
    protected $expire = 86400 * 7;//7天有效期
    protected $redisKey = 'shopify';

    public function __construct($config)
    {
        $this->redis = new \Redis();
        $this->redis->connect($config['hostname'], $config['port']);
        if (isset($config['auth']) && $config['auth']) {
            $this->redis->auth($config['auth']);
        }

        if (isset($config['expire']) && $config['expire']) {
            $this->expire = $config['expire'];
        }

        if (isset($config['key']) && $config['key']) {
            $this->redisKey = $config['key'];
        }
    }

    public function all(): array
    {
        return $this->redis->hGetAll($this->redisKey);
    }

    public function get(Session $session): array
    {
        $shop = $session->getShop();
        $string = $this->redis->hGet($this->redisKey, $shop);

        return $string ? json_decode($string, true) : [];
    }

    public function set(array $values, Session $session): void
    {
        $shop = $session->getShop();
        $this->redis->hSet($this->redisKey, $shop, json_encode($values));
    }

    public function push($value, Session $session): void
    {
        $values = $this->get($session);

        array_unshift($values, $value);

        $this->set($values, $session);
    }

    public function reset(Session $session): void
    {
        $this->redis->hDel($this->redisKey, $session->getShop());
    }
}