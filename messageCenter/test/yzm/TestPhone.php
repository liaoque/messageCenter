<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/27
 * Time: 9:08
 */
class Yzm_TestPhone
{
    public function indexFunc()
    {
        $appId = '7';
        $key = '77777777';
        $result = ProMessageCenter_Request::yzmPhone($appId, $key, '15012345678', 8);
        Test_ResponesBase::showMessage($result['code'] == 1, $result['mes']);
    }
}