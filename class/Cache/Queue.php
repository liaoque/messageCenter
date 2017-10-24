<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/27
 * Time: 9:32
 */


/**
 * 队列
 * Class Cache_Queue
 */
class Cache_Queue extends Cache_Base
{

    private $redis;

    public function __construct()
    {
        $this->setRedis(\PhpRedis::getInstance());
    }

    protected function getRedis()
    {
        return $this->redis;
    }

    protected function setRedis($redis)
    {
        $this->redis = $redis;
    }

    /**
     * 检查队列过期时间
     * @param null $cacheName
     * @return mixed
     */
    public function ttl($cacheName = null)
    {
        return $this->getRedis()->ttl($cacheName);
    }

    /**
     * 队列是否存在
     * @param null $cacheName
     * @return mixed
     */
    public function exists($cacheName = null)
    {
        return $this->getRedis()->exists($cacheName);
    }

    /**
     * 加入队列
     * @param $cacheName
     * @param $v
     * @return mixed
     */
    public function set($cacheName, $v)
    {
        $value = is_string($v) ? $v : json_encode($v);
        return $this->push($cacheName, $value);
    }


    /**
     * 加入队列
     * @param $cacheName
     * @param $v
     * @return mixed
     */
    protected function push($cacheName, $v)
    {
        return $this->getRedis()->lPush($cacheName, $v);
    }


    /**
     * 获取队列最后一个元素，并删除该元素
     * @param $cacheName
     * @return mixed
     */
    public function get($cacheName)
    {
        $result = $this->shift($cacheName);
        return $result;
    }

    /**
     * 获取队列最后一个元素，并删除该元素
     * @param $cacheName
     * @return mixed
     */
    protected function shift($cacheName)
    {
        return $this->getRedis()->rPop($cacheName);
    }

    /**
     * 获取队列长度
     * @param $cacheName
     * @return mixed
     */
    public function len($cacheName)
    {
        return $this->getRedis()->lLen($cacheName);
    }

    /**
     * 删除队列，慎用，执行后，直接删除该队列
     * @param null $cacheName
     * @return bool
     */
    public function del($cacheName = null)
    {

        return $cacheName && $this->getRedis()->del($cacheName);
    }

    public function remove($cacheName, $value, $count)
    {
        return $this->getRedis()->lRem($cacheName, $value, $count);
    }
}