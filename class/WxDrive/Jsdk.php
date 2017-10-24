<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/13
 * Time: 20:29
 */
class WxDrive_Jsdk extends WxDrive_Base
{
    const URL = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket';

    /**
     * @param $jsapiTicket
     * @param $url
     * @return array
     */
    public function init($jsapiTicket, $url)
    {
//        if (empty($jsapiTicket)) {
//            $jsapiTicket = self::getJsApiTicket();
//        }
//        if(!$url){
//            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
//            $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
//        }

        $timestamp = time();
        $nonceStr = WxDrive_Base::createNonceStr();
        $signature = $this->createSign($jsapiTicket, $nonceStr, $timestamp, $url);
        $signPackage = array(
            "appId" => $this->getConfig()->getAppId(),
            "nonceStr" => $nonceStr,
            "timestamp" => $timestamp,
            "url" => $url,
            "signature" => $signature
//            "rawString" => $string
        );
        return $signPackage;
    }

    private function createSign($jsapiTicket, $nonceStr, $timestamp, $url)
    {
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
        return $signature = sha1($string);
    }


    public function getJsApiTicket($accessToken)
    {
        $url = self::URL . '?' . http_build_query(array(
                'type' => 'jsapi',
                'access_token' => $accessToken
            ));
        $result = WxDrive_Base::curlGet($url);
        $result = json_decode($result, 1);
        if (!empty($result['errcode'])) {
            throw new Exception($result['errmsg'], $result['errcode']);
        }
        $result['expires_in'] = time() + $result['expires_in'];
        return $result;
    }


}