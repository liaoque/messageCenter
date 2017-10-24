<?php

/**
 * 集合
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/12
 * Time: 9:36
 */
class Cache_Gather extends Cache_Base
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
     * 获取集合中所有元素
     * @param null $cacheName
     * @return mixed
     */
    public function get($cacheName = null)
    {
        return $this->getRedis()->sMembers($cacheName);
    }

    /**
     * 随机获取一个或者多个元素
     * $this->getMemberByCount(xxxx); 返回所有的元素
     * $this->getMemberByCount(xxxx, 2); 返回2个元素
     * $this->getMemberByCount(xxxx, 10); 返回10个元素，如果只有5个元素，只返回5个
     * $this->getMemberByCount(xxxx, -2); 返回2个元素，有重复值
     * $this->getMemberByCount(xxxx, 6); 返回6个元素，有重复值，如果只有5个元素，但是还是返回6个，因为有重复值
     * @param $cacheName
     * @param null $count 为空，则随机获取一个元素
     * @return mixed
     */
    public function getMemberByCount($cacheName, $count = null)
    {
        if ($count) {
            return $this->getRedis()->sRandMember($cacheName, $count);
        }
        return $this->getRedis()->sRandMember($cacheName);
    }

    /**
     * 检查集合过期时间
     * @param null $cacheName
     * @return mixed
     */
    public function ttl($cacheName = null)
    {
        return $this->getRedis()->ttl($cacheName);
    }

    /**
     * 设置过期时间
     * @param null $cacheName
     * @param int $timeOut
     * @return bool
     */
    public function setTimeOut($cacheName = null, $timeOut = 0)
    {
        if ($this->exists($cacheName)) {
            return $this->getRedis()->expire($cacheName, $timeOut);
        }
        return false;
    }

    /**
     * 集合是否存在,或者集合中某个元素是否存在
     * @param null $cacheName
     * @param null $v
     * @return mixed
     */
    public function exists($cacheName = null, $v = null)
    {
        if ($v) {
            return $this->getRedis()->sIsMember($cacheName, $v);
        }
        return $this->getRedis()->exists($cacheName);
    }

    /**
     * 获取多个集合的交集
     * $this->sInter(array($cacheName1, $cacheName2));
     * $this->sInter($cacheName1, $cacheName2);
     * @param $cacheName1
     * @param $cacheName2
     * @return mixed
     */
    public function sInter($cacheName1, $cacheName2 = array())
    {
        if (is_array($cacheName1)) {
            return call_user_func_array(array($this->getRedis(), 'sInter'), $cacheName1);
        }
        return call_user_func_array(array($this->getRedis(), 'sInter'), func_get_args());
    }

    /**
     * 获取集合长度
     * @param $cacheName
     * @return mixed
     */
    public function len($cacheName)
    {
        return $this->getRedis()->sCard($cacheName);
    }

    /**
     * 把某项或者某几个元素放入队列
     * $this->set(xxx, 1, 2, 3)
     * $this->set(xxx, [1, 2, 3])
     * @param $cacheName
     * @param $v
     * @return mixed
     */
    public function set($cacheName, $v)
    {
        if (is_array($v)) {
            $v = array_values($v);
            array_unshift($v, $cacheName);
            $result = call_user_func_array(array($this->getRedis(), 'sAdd'), $v);
        } else {
            $result = $this->getRedis()->sAdd($cacheName, $v);
        }
        return $result;
    }

    /**
     * 删除集合，或者集合中某几个元素.
     * $this->del(xxx) 删除整个集合
     * $this->del(xxx, 2) 删除整个集合中两个元素
     * $this->del(xxx, [1, 4]) 删除集合中元素为1, 4的两个元素
     * @param null $cacheName
     * @param null $v
     * @return bool
     */
    public function del($cacheName = null, $v = null)
    {
        if (!$cacheName) {
            return false;
        }
        if (empty($v)) {
            $result = $this->getRedis()->del($cacheName);
        } elseif (is_array($v)) {
            $result = $this->remove($cacheName, $v);
        } else {
            $result = $this->removeCount($cacheName, $v);
        }
        return $result;
    }


    /**
     * 从集合中删除一个或者多个元素
     * $this->removeCount($cacheName)
     * @param $cacheName
     * @param null $count redis 版本大于3.2才可以用
     * @return mixed
     */
    public function removeCount($cacheName, $count = null)
    {
        if ($count) {
            $result = $this->getRedis()->sPop($cacheName, $count);
        } else {
            $result = $this->getRedis()->sPop($cacheName);
        }
        return $result;
    }

    /**
     * 从集合中删除一个或多个指定的元素
     * $this->remove(xxxx, 1)
     * $this->remove(xxxx, 1, 2, 3, 4)
     * $this->remove(xxxx, [1, 2 , 3, 4])
     * @param $cacheName
     * @param $v
     * @return mixed
     */
    public function remove($cacheName, $v)
    {
        $args = func_get_args();
        if (is_array($v)) {
            array_unshift($v, $cacheName);
            $args = $v;
        }
        return call_user_func_array(array($this->getRedis(), 'sRem'), $args);
    }


}