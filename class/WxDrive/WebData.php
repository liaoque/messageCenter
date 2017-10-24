<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/26
 * Time: 13:56
 */
class WxDrive_WebData
{
    private static $config = null;

    const KEY = 'wxWebData';

    private $data = array();

    public static function getInstance()
    {
        if (empty(self::$config)) {
            self::$config = new self();
        }
        return self::$config;
    }

    public function clear()
    {
        $key = self::KEY;
        unset($_COOKIE[$key]);
        setcookie($key, '', -1);
    }

    /**
     * $v 为空时, 则key必须是数组或者对象
     * @param $key
     * @param null $v
     * @param $authSave
     * @return $this
     */
    public function set($key, $v = null, $authSave = 0)
    {
        if ($v !== null) {
            $arr = array(
                $key => $v
            );
        } elseif (is_array($key) || is_object($key)) {
            $arr = $key;
        } else {
            return $this;
        }

        foreach ($arr as $key => $value) {
            $this->data[$key] = $value;
        }

        if ($authSave) {
            $this->save($authSave);
        }
        return $this;
    }

    /**
     * 保存数据
     * @param int $authSave
     */
    public function save($authSave = 7200)
    {
        $string = $this->encode();
        $key = self::KEY;
        $authSave = empty($authSave) ? 7200 : $authSave;
        setcookie($key, $string, time() + $authSave, '/');
    }


    /**
     * 重置数据
     */
    public function reset()
    {
        $key = self::KEY;
        $string = $_COOKIE[$key];
        $this->data = $this->decode($string);
//        var_dump($_COOKIE[$key], $this->data);
    }

    /**
     * 获取数据
     * @param null $key
     * @param bool $authReset
     * @return array
     */
    public function get($key = null, $authReset = false)
    {
        if ($authReset) {
            $this->reset();
        }
        if (empty($key)) {
            return $this->data;
        }
        return $this->data[$key];
    }


    /**
     * 加密
     * @return mixed|string
     */
    private function encode()
    {
        $string = json_encode($this->data);
        return authcode($string, 1);
    }

    /**
     * 解密
     * @param $string
     * @return array|mixed|null|stdClass
     */
    public function decode($string)
    {
        return json_decode(authcode($string), 1);
    }
}