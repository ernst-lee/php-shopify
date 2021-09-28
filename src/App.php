<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/8/18 0018
 * Time: 18:19
 */
namespace Shopify;

use Shopify\Core\ServiceContainer;

/**
 * Class App.
 *
 * @property Clients\Collection\Client                              $collection
 * @property Clients\Store\Country                                  $country
 * @property Clients\Store\Shop                                     $shop
 * @property Clients\Product\Client                                 $product
 * @property Clients\Order\Order                                    $order
 * @property Clients\Order\AbandonedOrder                           $abandonedOrder
 * @property Clients\Order\DraftOrder                               $draftOrder
 * @property Clients\Inventory\Client                               $inventory
 */
class App extends ServiceContainer {
    protected $providers = [
        Clients\Collection\ServiceProvider::class,
        Clients\Store\ServiceProvider::class,
        Clients\Product\ServiceProvider::class,
        Clients\Order\ServiceProvider::class,
        Clients\Inventory\ServiceProvider::class,
    ];
}