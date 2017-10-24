<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/20
 * Time: 9:48
 */


abstract class Cache_Base
{
    private $cacheName;

    private static $cacheHandl = array();

    public function getCacheName(){
        return $this->cacheName;
    }

    public function setCacheName($cacheName)
    {
        $this->cacheName = $cacheName;
    }

    abstract public function ttl($cacheName = null);

    abstract public function exists($cacheName = null);

    abstract public function set($cacheName, $v);


    public static function getInstance()
    {
        $className = get_called_class();
        if(empty(self::$cacheHandl[$className])){
            self::$cacheHandl[$className] = new $className;
        }
        return self::$cacheHandl[$className];
    }

    abstract public function del($cacheName = null);
}