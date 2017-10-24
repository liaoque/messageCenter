<?php


class Express_TestQuery
{

    public function indexFunc()
    {

        $result = ProMessageCenter_Request::expressQuery('1010', '612837876740');
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

