<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/26
 * Time: 17:38
 * hash容器 存储微信接口数据
 */
class WxDrive_ConsoleData
{
    private static $config = null;

    const KEY = 'wxConsoleData';

    public static function getInstance()
    {
        if (empty(self::$config)) {
            self::$config = new self();
        }
        return self::$config;
    }

    public function del($key = null)
    {
        $hashCache = Cache_Hash::getInstance();
        return $hashCache->del(self::KEY, $key);
    }

    /**
     * $key 为空时, 则 $value 必须是数组
     * @param $value
     * @param $key
     * @return $this
     */
    public function set($value, $key = null)
    {
        $hashCache = Cache_Hash::getInstance();
        $hashCache->set(self::KEY, $value, $key, -1);
        return $this;
    }


    /**
     * @param null $key
     * @return mixed
     */
    public function get($key = null)
    {
        $hashCache = Cache_Hash::getInstance();
        return $hashCache->get(self::KEY, $key);
    }

}