<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/13
 * Time: 10:39
 */
class Test_RequestBase
{

    /**
     * @param $url
     * @param $data
     * @param int $timeout
     * @param array $option
     * @return mixed
     */
    public function curlPost($url, $data, $timeout = 5, $option = array())
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
        }
        Test_ResponesBase::echoMessage('***************提交请求********************');
        Test_ResponesBase::echoMessage('提交URL: ' . $url);
        Test_ResponesBase::echoMessage('额外提交参数: ' . Model::enCode($option, true));
        Test_ResponesBase::echoMessage('提交数据: ' . (is_array($data) ? Model::enCode($data, true) : $data));
        Test_ResponesBase::echoMessage('返回接口: ' . $result);
        Test_ResponesBase::echoMessage('错误信息: ' . $msg);
        Test_ResponesBase::echoMessage('***************提交请求********************');
        curl_close($ch);
        return $result;
    }


    public function curlGet($url, $timeout = 5)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        if (!$result) {
            $msg = curl_error($ch);
        }
        Test_ResponesBase::echoMessage('***************提交请求********************');
        Test_ResponesBase::echoMessage('提交URL: ' . $url);
        Test_ResponesBase::echoMessage('返回接口: ' . $result);
        Test_ResponesBase::echoMessage('错误信息: ' . $msg);
        Test_ResponesBase::echoMessage('***************提交请求********************');
        curl_close($ch);
        return $result;
    }

    public function curlPostJson($url, $data, $timeout = 5)
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
        }
        Test_ResponesBase::echoMessage('***************提交请求********************');
        Test_ResponesBase::echoMessage('提交URL: ' . $url);
        Test_ResponesBase::echoMessage('提交数据: ' . (is_array($data) ? Model::enCode($data, true) : $data));
        Test_ResponesBase::echoMessage('返回接口: ' . $result);
        Test_ResponesBase::echoMessage('错误信息: ' . $msg);
        Test_ResponesBase::echoMessage('***************提交请求********************');
        curl_close($ch);
        return $result;
    }

    public function curlPostFile($url, $file, $timeout = 60)
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
        }
        Test_ResponesBase::echoMessage('***************提交请求********************');
        Test_ResponesBase::echoMessage('提交URL: ' . $url);
        Test_ResponesBase::echoMessage('上传文件名: ' . $file);
        Test_ResponesBase::echoMessage('返回接口: ' . $result);
        Test_ResponesBase::echoMessage('错误信息: ' . $msg);
        Test_ResponesBase::echoMessage('***************提交请求********************');
        curl_close($ch);
        return $result;
    }

    public function curlPostXml($url, $data, $timeout = 5)
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
        }
        Test_ResponesBase::echoMessage('***************提交请求********************');
        Test_ResponesBase::echoMessage('提交URL: ' . $url);
        Test_ResponesBase::echoMessage('提交数据: ' . (is_array($data) ? Model::enCode($data, true) : $data));
        Test_ResponesBase::echoMessage('返回接口: ' . $result);
        Test_ResponesBase::echoMessage('错误信息: ' . $msg);
        Test_ResponesBase::echoMessage('***************提交请求********************');
        curl_close($ch);
        return $result;
    }

    public function curlHttpsPostXml($url, $data, $timeout = 5)
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
        }
        Test_ResponesBase::echoMessage('***************提交请求********************');
        Test_ResponesBase::echoMessage('提交URL: ' . $url);
        Test_ResponesBase::echoMessage('提交数据: ' . (is_array($data) ? Model::enCode($data, true) : $data));
        Test_ResponesBase::echoMessage('返回接口: ' . $result);
        Test_ResponesBase::echoMessage('错误信息: ' . $msg);
        Test_ResponesBase::echoMessage('***************提交请求********************');
        curl_close($ch);
        return $result;
    }

    /**
     * @param $xml
     * @return array|mixed|null|stdClass
     */
    public function parseXml($xml)
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

