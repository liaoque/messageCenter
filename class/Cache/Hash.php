<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/20
 * Time: 11:42
 */
class Cache_Hash extends Cache_Base
{
    private $redis;

    const MAX_KEY_LEN = 200;

    private $maxKeyLen;

    const WRAING_MAX_LENGHT = -101;
    const ERROR_MAX_LENGHT = -2;
    const ERROR_MUST_ARRAY = -1;

    public function __construct()
    {
        $this->setRedis(\PhpRedis::getInstance());
        $this->maxKeyLen = self::MAX_KEY_LEN;
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
     * field 存在 则检查hash字段中 field 否则检查 hash是否存在
     * @param null $cacheName
     * @param  null $field 指定字段
     * @return mixed
     */
    public function exists($cacheName = null, $field = null)
    {
        return empty($field) ? $this->getRedis()->exists($cacheName) : $this->getRedis()->hExists($cacheName, $field);
    }

    /**
     * 设置哈希
     * @param $cacheName
     * @param $v 不指定$field的情况下请传递数组
     * @param  null $field 指定字段
     * @param int $timeOut 有效期
     * @return mixed
     * @throws Exception 如果cacheName值的总长度超过预设的最大长度，则会抛出错误
     */
    public function set($cacheName, $v, $field = null, $timeOut = 600)
    {
        $result = false;
        if (empty($field)) {
            $result = $this->setArray($cacheName, $v);
        } else {
            $result = $this->setKeyValue($cacheName, $v, $field);
        }
        $result && $timeOut != -1 && $this->setTimeOut($cacheName, $timeOut);
        return $result;
    }

    public function getMaxLen()
    {
        return $this->maxKeyLen;
    }

    public function setMaxLen($maxKeyLen)
    {
        return $this->maxKeyLen = $maxKeyLen;
    }

    /**
     * 对key进行赋值操作
     * @param $cacheName
     * @param $v
     * @return bool
     * @throws Exception $v必须是数组, 如果cacheName值的总长度超过预设的最大长度，则会抛出错误
     */
    public function setArray($cacheName, $v)
    {
        if (!is_array($v)) {
            throw new Exception($v . ' 必须是数组', self::ERROR_MUST_ARRAY);
        }
        $maxLength = $this->getMaxLen();
        $length = count($v);
        if ($length > $maxLength) {
            throw new Exception($length . ' 长度超过最大长度' . $maxLength, self::ERROR_MAX_LENGHT);
        }
        foreach ($v as $key => $value) {
            if (is_array($value) || is_object($value)) {
                $v[$key] = json_encode($value);
            }elseif(empty($value)){
                $v[$key] = -1;
            }
        }
        $result = $this->getRedis()->hMSet($cacheName, $v);
        $length = $this->len($cacheName);
        if ($result && $length - $maxLength > 0) {
            throw new Exception($cacheName . '最大长度:' . $maxLength . '|实际长度：' . $length, self::WRAING_MAX_LENGHT);
        }
        return $result;
    }

    /**
     * 更新某个Key中键的值
     * @param $cacheName
     * @param $v
     * @param $field
     * @return bool
     * @throws Exception 如果cacheName值的总长度超过预设的最大长度，则会抛出错误
     */
    public function setKeyValue($cacheName, $v, $field)
    {
        if(empty($v)){
            $value = -1;
        }else{
            $value = is_string($v) ? $v : json_encode($v);
        }
        $result = $this->getRedis()->hSet($cacheName, $field, $value);
        $length = $this->len($cacheName);
        $maxLength = $this->getMaxLen();
        if ($result && $length - $maxLength > 0) {
            throw new Exception($cacheName . '最大长度:' . $maxLength . '|实际长度：' . $length, self::WRAING_MAX_LENGHT);
        }
        return $result;
    }

    /**
     * 不指定$field的情况下 返回的是整个，指定的话返回的是是指定field
     * @param $cacheName
     * @param  null $field 指定字段| 可以是数组，则指定所个字段
     * @return mixed
     */
    public function get($cacheName, $field = null)
    {
        if (empty($field)) {
            $result = $this->getRedis()->hGetAll($cacheName);
        } else {
            if (!is_array($field)) {
                $field = array($field);
            }
            $result = $this->getRedis()->hMGet($cacheName, $field);
        }
        return $result;
    }

    /**
     * 获取hash 字段数量
     * @param $cacheName
     * @return mixed
     */
    public function len($cacheName)
    {
        return $this->getRedis()->hLen($cacheName);
    }

    /**
     * 不指定$field的情况下,删除的是整个 指定的话 删除的是指定field
     * @param null $cacheName
     * @param  null $field 指定字段
     * @return bool
     */
    public function del($cacheName = null, $field = null)
    {
        $result = false;
        if ($cacheName) {
            if (empty($field)) {
                $result = $this->getRedis()->del($cacheName);
            } else {
                $args = array_merge(array($cacheName), is_array($field) ? $field : array($field));
                $result = call_user_func_array(array($this->getRedis(), 'hDel'), $args);
            }
        }
        return $result;
    }

    /**
     * 删除热点数据
     * @param null $cacheName
     * @param null $field
     * @return bool
     */
    public function delHitList($cacheName = null, $field = null)
    {
        $result = $this->del($cacheName, $field);
        if ($result && $field) {
            $fields = is_array($field) ? $field : array($field);
            foreach ($fields as $field) {
                $queue = Cache_Queue::getInstance();
                $queue->remove($cacheName . 'Queue', $field, 1);
            }
        }
        return $result;
    }

    /**
     * 设置过期时间
     * @param $cacheName
     * @param int $timeOut
     * @return mixed
     */
    public function setTimeOut($cacheName, $timeOut = 10)
    {
        return $this->getRedis()->expire($cacheName, $timeOut);
    }

    /**
     * 设置热点数据过期时间，热点数据默认是永久存在的
     * @param $cacheName
     * @param int $timeOut
     * @return mixed
     */
    public function setHitListTimeOut($cacheName, $timeOut = 10)
    {
        $sortQueue = $cacheName . 'Queue';
        $this->getRedis()->expire($sortQueue, $timeOut);
        return $this->getRedis()->expire($cacheName, $timeOut);
    }

    /**
     * 热点数据, 且常驻, 规定一个预设长度（默认200），如果超过这个限定长度，则会把访问最少的那一条数据删掉
     * 初始化的时候，如果数据大于200则会抛出错误
     * 添加的情况下，如果数据大于200则不会报错
     * @param $cacheName
     * @param $v
     * @param null $field
     * @return bool
     * @throws Exception   只有在$v是多维数组的情况下，如果cacheName值的总长度超过预设的最大长度，则会抛出错误
     */
    public function addHotList($cacheName, $v, $field = null)
    {
        $sortQueue = $cacheName . 'Queue';
        $queue = Cache_Queue::getInstance();
        try {
            $result = $this->set($cacheName, $v, $field, -1);
        } catch (Exception $e) {
            if ($e->getCode() !== self::WRAING_MAX_LENGHT) {
                throw $e;
            }
            for ($i = 0; $i < $this->len($cacheName) - $this->getMaxLen(); $i++) {
                //把队列中的$field 取出来，然后删除
                $member = $queue->get($sortQueue);
                $this->del($cacheName, $member);
            }
        }
        if (!$field) {
            $fields = array_keys($v);
        } else {
            $fields = is_array($field) ? $field : array($field);
        }
        foreach ($fields as $field) {
            //把 field加入队列
//            $queue->remove($cacheName, $field, 1);
            $queue->set($sortQueue, $field);
        }
        return $result;
    }


}