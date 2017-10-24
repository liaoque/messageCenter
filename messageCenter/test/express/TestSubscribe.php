<?php


class Express_TestSubscribe
{

    public function indexFunc()
    {
//        $appId = '4';
//        $templateId = '2';
//        $toMail = '844596330@qq.com';
//        $content = [
//            'name' => 'aaaaaa'
//        ];
//        $key = '1111111';
//        $result = ProMessageCenter_Request::senEmail($appId, $templateId, $toMail, $content, $key);
//        Test_ResponesBase::showMessage($result['code'] == 1, $result['mes']);
        $appId = '7';
        $companyNum = '1010';
        $waybill = '974556280915';
        $from = '杭州';
        $to = '苏州';
        $key = '77777777';
        $result = ProMessageCenter_Request::expressSubcribe($appId, $companyNum, $waybill, $from, $to, $key);
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

