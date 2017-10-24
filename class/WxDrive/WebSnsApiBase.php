<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/26
 * Time: 15:53
 */
class WxDrive_WebSnsApiBase extends WxDrive_WebAuth
{

    //appid=APPID&redirect_uri=REDIRECT_URI&response_type=code&scope=SCOPE&state=STATE#wechat_redirect
    public static function authorize($config, $redirectUri, $state = '', $return = 1)
    {
        /**
         * appid    是    公众号的唯一标识
         * redirect_uri    是    授权后重定向的回调链接地址，请使用urlencode对链接进行处理
         * response_type    是    返回类型，请填写code
         * scope    是    应用授权作用域，snsapi_base （不弹出授权页面，直接跳转，只能获取用户openid），snsapi_userinfo （弹出授权页面，可通过openid拿到昵称、性别、所在地。并且，即使在未关注的情况下，只要用户授权，也能获取其信息）
         * state    否    重定向后会带上state参数，开发者可以填写a-zA-Z0-9的参数值，最多128字节
         * #wechat_redirect    是    无论直接打开还是做页面302重定向时候，必须带此参数
         */

        $sendData = array(
            'appid' => $config->getAppId(),
            'redirect_uri' => $redirectUri,
            'response_type' => self::RESPONSE_TYPE,
            'scope' => self::SNSAPI_BASE,
            'state' => $state
        );

        $url = self::SNS_API_GET_CODE_URL . '?' . http_build_query($sendData) . '#wechat_redirect';
        if ($return) {
            return $url;
        }
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: $url");
        exit();
    }


}