<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/13
 * Time: 12:36
 */
class WxDrive_ServiceMsgAll
{

    const sendTextUrl = 'https://api.weixin.qq.com/cgi-bin/message/mass/sendall';

    /**
     * 文本消息
     * @param $accessToken
     * @param $content
     * @return mixed
     */
    public function sendText($accessToken, $content)
    {
        $c = [
            'filter' => [
                "is_to_all" => true,
                "tag_id" => ''
            ],
            "text" => [
                "content" => $content
            ],
            "msgtype" => "text"

        ];
        $url = self::sendTextUrl . '?' . http_build_query(array(
                'access_token' => $accessToken
            ));
        return WxDrive_Base::curlPostJson($url, $c);
    }
}