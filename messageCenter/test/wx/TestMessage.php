<?php

class Wx_TestMessage
{
    public function oneFunc()
    {
        $appId = '7';
        $key = '77777777';
        $openId = 'o5U2mwC6QziRQgOBWdjI8FAb8Bao';
        $templateId = '13';
        $content = [
            'first' => 'nihao',
            'keyword1' => '110',
            'keyword2' => '警察叔叔233222',
            'keyword3' => '你被抓了',
            'keyword4' => '坦白从宽',
            'remark' => '抗拒从严'
        ];
        $ext = [
            'url' => 'http://baidu.com',
            'appid' => '',
            'pagepath' => '',
        ];
        $result = ProMessageCenter_Request::wxTemplateOne($appId, $key, $openId, $templateId, $content, $ext);
        Test_ResponesBase::showMessage($result['code'] == 1, $result['mes']);
    }

    public function allFunc()
    {
        $appId = '7';
        $key = '77777777';
        $content = [
            'content' => '32132132132131',
        ];
        $result = ProMessageCenter_Request::wxTemplateAll($appId, $key, $content);
        Test_ResponesBase::showMessage($result['code'] == 1, $result['mes']);
    }

}