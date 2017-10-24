<?php


class WxDrive_ServiceTemplateMsg
{

    const sendTextUrl = 'https://api.weixin.qq.com/cgi-bin/message/template/send';

    /**
     * @param $accessToken
     * @param $openId
     * @param $templateId
     * @param $data
     * @param $url
     * @param array $miniprogram
     *                  "miniprogram":[
     *                         "appid"=>"xiaochengxuappid12345",
     *                          "pagepath"=>"index?foo=bar"
     *                   ]
     * @return array|mixed|null|stdClass
     * @throws Exception
     */
    public function sendText($accessToken, $openId, $templateId, $data, $url, $miniprogram = array())
    {
        $c = array(
            'touser' => $openId,
            'template_id' => $templateId,
            'url' => $url,
            'data' => $data
        );
        if (!empty($miniprogram)) {
            $c['miniprogram'] = [
                "appid" => $miniprogram['appid'],
                "pagepath" => $miniprogram['pagepath']
            ];
        }
        $url = self::sendTextUrl . '?' . http_build_query(array(
                'access_token' => $accessToken
            ));
        $xml = WxDrive_Base::curlPostJson($url, $c);
        $result = Model::deCode($xml);
        if (empty($result)) {
            throw new  Exception('请求失败, 网络错误', -500);
        }
        if ($result['errcode']) {
            throw new  Exception('请求失败, [code:' . $result['errcode'] . ', errmsg: ' . $result['errmsg'] . ']', -500);
        }
        return $result['msgid'];
    }

}