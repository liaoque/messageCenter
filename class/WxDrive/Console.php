<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/1
 * Time: 16:22
 * 微信控制台接口
 */
class WxDrive_Console extends WxDrive_Base
{
    const getAccessTokenUrl = 'https://api.weixin.qq.com/cgi-bin/token';
    const getWxServerIpUrl = 'https://api.weixin.qq.com/cgi-bin/getcallbackip';


    /**
     *获取AccessToken
     */
    public function getAccessToken()
    {
        /**
         * grant_type    是    获取access_token填写 client_credential
         * appid    是    第三方用户唯一凭证
         * secret    是    第三方用户唯一凭证密钥，即appsecret
         */
        $getData = array(
            'grant_type' => 'client_credential',
            'appid' => $this->getConfig()->getAppId(),
            'secret' => $this->getConfig()->getAppSecret()
        );
        $url = self::getAccessTokenUrl . '?' . http_build_query($getData);

        /**
         * access_token    获取到的凭证
         * expires_in    凭证有效时间，单位：秒
         */
        $result2 = WxDrive_Base::curlGet($url);
        $result = json_decode($result2, 1);
        if (!empty($result['errcode'])) {
            throw new Exception($result['errmsg'], $result['errcode']);
        }
        $result['expires_in'] = time() + $result['expires_in'];
        return $result;
    }

    public static function toSting($val)
    {
        return $val . '';
    }

    /**
     * 获取微信服务器IP地址
     */
    public function getWxServerIp($accessToken)
    {

        //https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=ACCESS_TOKEN
        /**
         * access_token    是    公众号的access_token
         */
        $getData = array(
            'access_token' => $accessToken
        );
        $url = self::getWxServerIpUrl . '?' . http_build_query($getData);
    }


}