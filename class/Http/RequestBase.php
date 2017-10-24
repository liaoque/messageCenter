<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/13
 * Time: 10:39
 */
class Http_RequestBase
{

    /**
     * @param $url
     * @param $data
     * @param int $timeout
     * @param array $option
     * @return mixed
     */
    public static function curlPost($url, $data, $timeout = 5, $option = array())
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        foreach ($option as $key => $value) {
            curl_setopt($ch, $key, $value);
        }

        $result = curl_exec($ch);
        if (!$result) {
            $msg = curl_error($ch);
            log_message('RequestBase', 'curlPost:[' . $data . '] error: [' . $msg . ']', 'RequestBase');
        }
        curl_close($ch);
        return $result;
    }


    public static function curlGet($url, $timeout = 5)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        if (!$result) {
            $msg = curl_error($ch);
            log_message('RequestBase', 'curlGet:[' . $url . '] error: [' . $msg . ']', 'RequestBase');
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
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );
        $result = curl_exec($ch);
        if (!$result) {
            $msg = curl_error($ch);
            log_message('RequestBase', 'curlPostJson:[' . $data . '] error: [' . $msg . ']', 'RequestBase');
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
            log_message('RequestBase', 'curlPostFile:[' . $file . '] error: [' . $msg . ']', 'RequestBase');
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
            log_message('RequestBase', 'curlPostXml:[' . $data . '] error: [' . $msg . ']', 'RequestBase');
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
            log_message('RequestBase', 'curlHttpsPostXml:[' . $data . '] error: [' . $msg . ']', 'RequestBase');
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


}

