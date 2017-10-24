<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/1
 * Time: 16:09
 *
 * 验证服务器地址的有效性
 */
class WxDrive_Sample
{
    public static function valid()
    {
        $echoStr = $_GET["echostr"];
        if (self::checkSignature()) {
            echo $echoStr;
            exit;
        }

    }

    private static function checkSignature()
    {
        $config = WxDrive_Config::getInstance();

        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = $config->getToken();
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        return $tmpStr == $signature;
    }

}