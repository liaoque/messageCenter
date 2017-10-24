<?php

/**
 * 退款接口
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/8
 * Time: 10:57
 */
class Pay_TestRefound
{
    public function indexFunc()
    {
        $appId = '7';
        $sn = 'S7S1S6S20170613193126S71';
        $key = '77777777';
        $result = ProMessageCenter_Request::payClose($appId, $sn, $key);
        Test_ResponesBase::showMessage($result['code'] == 1, Model::enCode($result['mes']));
    }
}