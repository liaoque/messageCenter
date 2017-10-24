<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/13
 * Time: 12:36
 */
class WxDrive_ServiceMsg extends WxDrive_Console
{

    const sendTextUrl = 'https://api.weixin.qq.com/cgi-bin/message/custom/send';

    /**
     * 文本消息
     * @param $accessToken
     * @param $openId
     * @param $content
     * @return mixed
     */
    public function sendText($accessToken, $openId, $content)
    {
        $c = array(
            'touser' => $openId,
            'msgtype' => 'text',
            'text' => array(
                "content" => $content
            )
        );
        $url = self::sendTextUrl . '?' . http_build_query(array(
                'access_token' => $accessToken
            ));
        return WxDrive_Base::curlPostJson($url, $c);
    }

    /**
     * 图片消息
     * @param $accessToken
     * @param $openId
     * @param $content
     * @return mixed
     */
    public function sendImage($accessToken, $openId, $content)
    {
        $c = array(
            'touser' => $openId,
            'msgtype' => 'image',
            'text' => array(
                "media_id" => $content
            )
        );
        $url = self::sendTextUrl . '?' . http_build_query(array(
                'access_token' => $accessToken
            ));
        return WxDrive_Base::curlPostJson($url, $c);
    }

    /**
     * 语音消息
     * @param $accessToken
     * @param $openId
     * @param $content
     * @return mixed
     */
    public function sendVoice($accessToken, $openId, $content)
    {
        $c = array(
            'touser' => $openId,
            'msgtype' => 'video',
            'voice' => array(
                "media_id" => $content
            )
        );
        $url = self::sendTextUrl . '?' . http_build_query(array(
                'access_token' => $accessToken
            ));
        return WxDrive_Base::curlPostJson($url, $c);
    }

    /**
     * 发送视频消息
     * @param $accessToken
     * @param $openId
     * @param $mediaId
     * @param $thumbMediaId
     * @param null $title
     * @param null $description
     * @return mixed
     */
    public function sendThumbMedia($accessToken, $openId, $mediaId, $thumbMediaId, $title = null, $description = null)
    {
        $c = array(
            'touser' => $openId,
            'msgtype' => 'video',
            'video' => array(
                "media_id" => $mediaId,
                "thumb_media_id" => $thumbMediaId,
                "title" => $title,
                "description" => $description
            )
        );
        $url = self::sendTextUrl . '?' . http_build_query(array(
                'access_token' => $accessToken
            ));
        return WxDrive_Base::curlPostJson($url, $c);
    }


    /**
     * 发送音乐消息
     * @param $accessToken
     * @param $openId
     * @param null $title
     * @param null $description
     * @param null $musicURL
     * @param null $hQMusicUrl
     * @param null $thumbMediaId
     * @return mixed
     */
    public function sendMusic($accessToken, $openId, $title = null, $description = null, $musicURL = null, $hQMusicUrl = null, $thumbMediaId = null)
    {
        $c = array(
            'touser' => $openId,
            'msgtype' => 'music',
            'music' => array(
                "title" => $title,
                "description" => $description,
                "musicurl" => $musicURL,
                "hqmusicurl" => $hQMusicUrl,
                "thumb_media_id" => $thumbMediaId,
            )
        );
        $url = self::sendTextUrl . '?' . http_build_query(array(
                'access_token' => $accessToken
            ));
        return WxDrive_Base::curlPostJson($url, $c);
    }

    /**
     * 发送图文消息（点击跳转到外链） 图文消息条数限制在8条以内，注意，如果图文数超过8，则将会无响应
     * @param $accessToken
     * @param $openId
     * @param array $news 二维数组，新闻列表，每项都有下面这几个参数
     *          "title":"Happy Day",
     *          "description":"Is Really A Happy Day",
     *          "url":"URL",
     *          "picurl":"PIC_URL"
     * @return mixed
     */
    public function sendNews($accessToken, $openId, $news)
    {
        if (count($news) > 8) {
            return false;
        }
        $c = array(
            'touser' => $openId,
            'msgtype' => 'news',
            'news' => array(
                "articles" => $news
            )
        );
        $url = self::sendTextUrl . '?' . http_build_query(array(
                'access_token' => $accessToken
            ));
        return WxDrive_Base::curlPostJson($url, $c);
    }


    /**
     * 发送图文消息（点击跳转到图文消息页面） 图文消息条数限制在8条以内，注意，如果图文数超过8，则将会无响应。
     * @param $accessToken
     * @param $openId
     * @param $content
     * @return mixed
     */
    public function sendMpNews($accessToken, $openId, $content)
    {
        $c = array(
            'touser' => $openId,
            'msgtype' => 'mpnews',
            'text' => array(
                "media_id" => $content
            )
        );
        $url = self::sendTextUrl . '?' . http_build_query(array(
                'access_token' => $accessToken
            ));
        return WxDrive_Base::curlPostJson($url, $c);
    }

    /**
     * 获取 ApiTicket
     * @param $accessToken
     * @return array|bool|mixed|null|stdClass
     */
    private function getApiTicket($accessToken)
    {
        if (empty($accessToken)) {
            return false;
        }
        $url = self::URL . '?' . http_build_query(array(
                'type' => 'WxDrive_card',
                'access_token' => $accessToken
            ));
        $result = WxDrive_Base::curlGet($url);
        $result = json_decode($result, 1);
        /**
         * 返回
         * errcode    错误码
         * errmsg    错误信息
         * ticket    api_ticket，卡券接口中签名所需凭证
         * expires_in    有效时间
         */
        return $result;
    }


    /**
     * @param $openId
     * @param null $apiTicket
     * @param null $accessToken
     * @return array
     */
    public function createWxCardExt($openId, $cardId, $apiTicket = null, $accessToken = null)
    {
        $code = '';
        $timestamp = time();
        $nonceStr = WxDrive_Base::createNonceStr();
        if (empty($apiTicket)) {
            $apiTicket = $this->getApiTicket($accessToken);
        }
        //将 api_ticket、timestamp、card_id、code、openid、nonce_str的value值进行字符串的字典序排序
        $data = array(
            $timestamp,
            $code,
            $apiTicket['api_ticket'],
            $cardId,
            $openId,
            $nonceStr
        );
        asort($data);
        $str = '';
        foreach ($data as $v) {
            $str .= $v;
        }
        $signature = sha1($str);
        return array(
            'code' => $code,
            'openid' => $openId,
            'timestamp' => $timestamp,
            'nonce_str' => $nonceStr,
            'api_ticket ' => $apiTicket['api_ticket'],
            'signature' => $signature,
            'expires_in ' => $apiTicket['expires_in']
        );
    }

    /**
     * 发送卡券
     * 特别注意客服消息接口投放卡券仅支持非自定义Code码的卡券。
     * @param $accessToken
     * @param $openId
     * @param $cardId
     * @param $cardExt 使用 createWxCardExt创建
     * @return mixed
     */
    public function sendWxCard($accessToken, $openId, $cardId, $cardExt)
    {
        $c = array(
            'touser' => $openId,
            'msgtype' => 'wxcard',
            'wxcard' => array(
                "card_id" => $cardId,
                "card_ext" => $cardExt
            )
        );
        $url = self::sendTextUrl . '?' . http_build_query(array(
                'access_token' => $accessToken
            ));
        return WxDrive_Base::curlPostJson($url, $c);
    }

    /**
     * 使用客服帐号
     * @param $accessToken
     * @param $openId
     * @param $centent
     * @param $account
     * @return mixed
     */
    public function sendCustomService($accessToken, $openId, $centent, $account)
    {
        $c = array(
            'touser' => $openId,
            'msgtype' => 'wxcard',
            "text" => array(
                "content" => $centent
            ),
            "customservice" => array(
                "kf_account" => $account
            )
        );
        $url = self::sendTextUrl . '?' . http_build_query(array(
                'access_token' => $accessToken
            ));
        return WxDrive_Base::curlPostJson($url, $c);
    }


}