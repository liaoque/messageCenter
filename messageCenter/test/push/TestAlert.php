<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/13
 * Time: 17:07
 */
class Push_TestAlert
{
    public function oneFunc()
    {

        $appId = '7';
        $key = '77777777';
        $uid = 4;
        $templateId = 12;
        $content = [
            'alter'=> 333,
            'text2'=> 444,
        ];
        $result = ProMessageCenter_Request::pushAlertOne($appId, $key, $uid, $templateId, $content);
        Test_ResponesBase::showMessage($result['code'] == 1, $result['mes']);
    }

    public function allFunc()
    {
        $appId = '7';
        $key = '77777777';
        $templateId = 12;
        $content = [
            'alter'=> 1111,
            'text2'=> 2222,
        ];
        $result = ProMessageCenter_Request::pushAlertAll($appId, $key, $templateId, $content);
        Test_ResponesBase::showMessage($result['code'] == 1, $result['mes']);
    }


}