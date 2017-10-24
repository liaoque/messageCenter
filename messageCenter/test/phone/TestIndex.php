<?php


class Phone_TestIndex
{

    public function indexFunc()
    {
        $appId = '7';
        $templateId = '8';
        $phone = '15012345678';
        $content = [
            'text' => 'aaaaaa'
        ];
        $key = '77777777';
        $result = ProMessageCenter_Request::sendPhone($appId, $templateId, $phone, $content, $key);
        Test_ResponesBase::showMessage($result['code'] == 1, $result['mes']);


//        $appId = '4';
//        $templateId = '0';
//        $toMail = '844596330@qq.com';
//        $content = 'aaaaaa';
//        $key = '1111111';
//        $result = ProMessageCenter_Request::senEmail($appId, $templateId, $toMail, $content, $key);
//        Test_ResponesBase::showMessage($result['code'] == 1, $result['mes']);
    }
}

