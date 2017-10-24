<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/26
 * Time: 9:24
 */
class WxDrive_Base
{

    protected $config = null;

    public function __construct(WxDrive_Config $config)
    {
        $this->setConfig($config);
    }


    /**
     * 设置config对象
     * @param $config @WxDrive_Config对象
     */
    protected function setConfig(/*WxDrive_Config*/
        $config)
    {
        $this->config = $config;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public static function curlPost($url, $data, $timeout = 5)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        if (!$result) {
            $msg = curl_error($ch);
            log_message('CURL', '微信curlPost:[' . $data . '] error: [' . $msg . ']', 'WxDrive_curl');
        }
        curl_close($ch);
        return $result;
    }

    public static function curlPostJson($url, $data, $timeout = 5)
    {
        if (is_array($data)) {
            $data = json_encode($data);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );
        $result = curl_exec($ch);
        if (!$result) {
            $msg = curl_error($ch);
            log_message('CURL', '微信curlPostJson:[' . $data . '] error: [' . $msg . ']', 'WxDrive_curl');
        }
        curl_close($ch);
        return $result;
    }

    public static function curlPostFile($url, $file, $timeout = 60)
    {
        $ch = curl_init();
        if (class_exists('CURLFile')) {
            $data = array(
                'file' => new CURLFile($file)
            );
            curl_setopt($ch, CURLOPT_SAFE_UPLOAD, 1);
        } else {
            $data = array(
                'file' => '@' . $file
            );
            if (defined('CURLOPT_SAFE_UPLOAD')) {
                curl_setopt($ch, CURLOPT_SAFE_UPLOAD, 0);
            }
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        if (!$result) {
            $msg = curl_error($ch);
            log_message('CURL', '微信curlPostFile:[' . $file . '] error: [' . $msg . ']', 'WxDrive_curl');
        }
        curl_close($ch);
        return $result;
    }

    public static function curlPostXml($url, $data, $timeout = 5)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: text/xml',
                'Content-Length: ' . strlen($data))
        );
        $result = curl_exec($ch);
        if (!$result) {
            $msg = curl_error($ch);
            log_message('CURL', '微信curlPostXml:[' . $data . '] error: [' . $msg . ']', 'WxDrive_curl');
        }
        curl_close($ch);
        return $result;
    }

    public static function curlHttpsPostXml($url, $data, $timeout = 5)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: text/xml',
                'Content-Length: ' . strlen($data))
        );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        if (!$result) {
            $msg = curl_error($ch);
            log_message('CURL', '微信curlHttpsPostXml:[' . $data . '] error: [' . $msg . ']', 'WxDrive_curl');
        }
        curl_close($ch);
        return $result;
    }

    /**
     * @param $xml
     * @return array|mixed|null|stdClass
     */
    public static function parseXml($xml)
    {
        if (empty($xml)) {
            return array();
        }
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        if (empty($values)) {
            return array();
        }
        return $values;
    }


    public static function curlGet($url, $timeout = 5)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * 过滤空值， 并字典排序, 创建签名， 并返回 签名和 过滤并排序后的数组
     * @param $data
     * @param $key
     * @return array
     */
    public static function createWordbookSignFilterEmpty($data, $key)
    {
        ksort($data);
        $signData = array();
        foreach ($data as $k => $v) {
            if (!empty($v)) {
                $signData[$k] = $v;
            }
        }
        $signData['key'] = $key;
        $sign = urldecode(http_build_query($signData));
        $sign = strtoupper(md5($sign));

        unset($signData['key']);
        return array(
            'data' => $signData,
            'sign' => $sign
        );
    }


    public static function createNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

}