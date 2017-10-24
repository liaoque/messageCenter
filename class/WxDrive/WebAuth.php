<?php

/**
 * 微信 网页授权
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/26
 * Time: 16:01
 */
abstract class WxDrive_WebAuth extends WxDrive_Base
{

    const RESPONSE_TYPE = 'code';

    const SNSAPI_BASE = 'snsapi_base';

    const SNSAPI_USERINFO = 'snsapi_userinfo';

    //用户同意授权，获取code
    const SNS_API_GET_CODE_URL = 'https://open.weixin.qq.com/connect/oauth2/authorize';

    //通过code换取网页授权access_token
    const SNS_API_GET_ACCESS_TOKEN_URL = 'https://api.weixin.qq.com/sns/oauth2/access_token';

    //刷新access_token（如果需要）refresh_token
    const SNS_API_REFRESH_TOKEN_URL = 'https://api.weixin.qq.com/sns/oauth2/refresh_token';

    //拉取用户信息(需scope为 snsapi_userinfo)
    const SNS_API_GET_USER_INFO_URL = 'https://api.weixin.qq.com/sns/userinfo';

    //检验授权凭证（access_token）是否有效
    const SNS_API_CHECK_AUTH_URL = 'https://api.weixin.qq.com/sns/auth';


    /**
     * @param $config WxDrive_Config对象
     * @param $redirectUri 授权后重定向的回调链接地址，请使用urlencode对链接进行处理
     * @param $state  重定向后会带上state参数，开发者可以填写a-zA-Z0-9的参数值，最多128字节
     * @return mixed
     */
    public static function authorize($config, $redirectUri, $state = '')
    {
    }

    public static function accessTokenKey()
    {
        return 'accessToken';
    }


    /**
     * 如果获取失败，则返回空数组
     * 获取成功，但是过期且刷新失败，返回空数组
     * 获取成功 返回AccessToken的数据
     * @return array|mixed
     */
    public function getAccessToken(/*$isAjax = false*/)
    {
        /**
         * 从WxDrive_WebData中取出数据，如果没有取到，则重新授权
         */
        $key = self::accessTokenKey();
        $wxWebData = WxDrive_WebData::getInstance();
        $accInfo = $wxWebData->get($key, true);
        if (!empty($accInfo) && $accInfo['endTime'] < time()) {
            /**
             * 如果时间过期了, 重新刷新
             */
            $accInfo = $this->refreshToken($accInfo['refresh_token']);
            if (!empty($accInfo['errcode'])) {
                //如果刷新错误，则重新授权
                $accInfo = array();
            }
        }

        return $accInfo;
    }

    /**
     * @param $code 为空,只从redis中读取
     * @return mixed
     */
    public function accessToken($code = null)
    {
        $accInfo = $this->getAccessToken();
        if (!empty($accInfo)) {
            return $accInfo;
        }

        /**
         * appid    是    公众号的唯一标识
         * secret    是    公众号的appsecret
         * code    是    填写第一步获取的code参数
         * grant_type    是    填写为authorization_code
         */
        $sendData = array(
            'appid' => $this->getConfig()->getAppId(),
            'secret' => $this->getConfig()->getAppSecret(),
            'code' => $code,
            'grant_type' => 'authorization_code',
        );
        $url = self::SNS_API_GET_ACCESS_TOKEN_URL . '?' . http_build_query($sendData);
        $accInfo = WxDrive_Base::curlGet($url);
        log_message('error', '获取accesss：url:'.$url.'['.json_encode($accInfo).']', 'wx');
        /**
         * 返回值
         * access_token    网页授权接口调用凭证,注意：此access_token与基础支持的access_token不同
         * expires_in    access_token接口调用凭证超时时间，单位（秒）
         * refresh_token    用户刷新access_token
         * openid    用户唯一标识，请注意，在未关注公众号时，用户访问公众号的网页，也会产生一个用户和公众号唯一的OpenID
         * scope    用户授权的作用域，使用逗号（,）分隔
         *
         * 错误时微信会返回JSON数据包如下
         * {"errcode":40029,"errmsg":"invalid code"}
         */
        $result = json_decode($accInfo, 1);

        if (empty($result['errcode'])) {
            /**
             * 把数据保存到 WxDrive_WebData
             */
            $key = self::accessTokenKey();
            $wxWebData = WxDrive_WebData::getInstance();
            $result['endRef'] = 7;
            $result['endTime'] = time() + $result['expires_in'] - 10;
            $wxWebData->set($key, $result, 86400 * $result['endRef']);
            log_message('error', '设置accesss：'.'['.json_encode($result).']', 'wx');
        }
        return $result;
    }

    /*appid	是	公众号的唯一标识
    grant_type	是	填写为refresh_token
    refresh_token	是	填写通过access_token获取到的refresh_token参数*/
    /*
     * 返回
     * access_token	网页授权接口调用凭证,注意：此access_token与基础支持的access_token不同
    expires_in	access_token接口调用凭证超时时间，单位（秒）
    refresh_token	用户刷新access_token
    openid	用户唯一标识
    scope	用户授权的作用域，使用逗号（,）分隔*/

    /**
     * @param $refreshToken
     * @return mixed
     */
    public function refreshToken($refreshToken, $endRef = 7)
    {
        /**
         * appid    是    公众号的唯一标识
         * grant_type    是    填写为refresh_token
         * refresh_token    是    填写通过access_token获取到的refresh_token参数
         */
        $sendData = array(
            'appid' => $this->getConfig()->getAppId(),
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken
        );

        $url = self::SNS_API_REFRESH_TOKEN_URL . '?' . http_build_query($sendData);
        $accInfo = self::curlGet($url);
        log_message('error', '重新获取accesss：url：'.$url.'['.json_encode($accInfo).']', 'wx');
        /**
         * 返回值
         * access_token    网页授权接口调用凭证,注意：此access_token与基础支持的access_token不同
         * expires_in    access_token接口调用凭证超时时间，单位（秒）
         * refresh_token    用户刷新access_token
         * openid    用户唯一标识
         * scope    用户授权的作用域，使用逗号（,）分隔
         *
         * 错误
         * {"errcode":40029,"errmsg":"invalid code"}
         */
        $result = json_decode($accInfo, 1);
        if (empty($result['errcode'])) {
            $timeOut = array(
                7 => 30,
                30 => 60,
                60 => 90,
                90 => 90
            );
            $key = self::accessTokenKey();
            $result['endRef'] = $timeOut[$endRef];
            $wxWebData = WxDrive_WebData::getInstance();
            $result['endTime'] = time() + $result['expires_in'] - 10;
            $wxWebData->set($key, $result, 86400 * $result['endRef']);
            log_message('error', '重置accesss：'.'['.json_encode($result).']', 'wx');
        }
        return $result;

    }

    /**
     * @param $accessToken
     * @return mixed
     */
    public function snsapiUserinfo($accessToken)
    {
        /**
         * access_token    网页授权接口调用凭证,注意：此access_token与基础支持的access_token不同
         * openid    用户的唯一标识
         * lang    返回国家地区语言版本，zh_CN 简体，zh_TW 繁体，en 英语
         */
        $sendData = array(
            'openid' => $this->getConfig()->getAppId(),
            'access_token' => $accessToken,
            'lang' => 'zh_CN',
        );
        $url = self::SNS_API_GET_USER_INFO_URL . '?' . http_build_query($sendData);
        $result = self::curlGet($url);
        /**
         * openid    用户的唯一标识
         * nickname    用户昵称
         * sex    用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
         * province    用户个人资料填写的省份
         * city    普通用户个人资料填写的城市
         * country    国家，如中国为CN
         * headimgurl    用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空。若用户更换头像，原有头像URL将失效。
         * privilege    用户特权信息，json 数组，如微信沃卡用户为（chinaunicom）
         * unionid    只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。详见：获取用户个人信息（UnionID机制）
         *
         * 错误信息
         * {"errcode":40003,"errmsg":" invalid openid "}
         */
        $result = json_decode($result, 1);
        return $result;

    }


    public function checkAuth($accessToken)
    {

        /**
         * access_token    网页授权接口调用凭证,注意：此access_token与基础支持的access_token不同
         * openid    用户的唯一标识
         */
        $sendData = array(
            'access_token' => $accessToken,
            'openid' => $this->getConfig()->getAppId()
        );

        $url = self::SNS_API_CHECK_AUTH_URL . '?' . http_build_query($sendData);
        $result = self::curlGet($url);
        /**
         * 正确
         * { "errcode":0,"errmsg":"ok"}
         * 错误
         * { "errcode":40003,"errmsg":"invalid openid"}
         */
        $result = json_decode($result, 1);
        return $result;
    }


}