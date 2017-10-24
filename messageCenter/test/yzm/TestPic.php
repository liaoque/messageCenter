<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/27
 * Time: 9:08
 */
class Yzm_TestPic
{
    public function indexFunc()
    {
        $appId = '7';
        $key = '77777777';
        $result = ProMessageCenter_Request::yzmPic($appId, $key);
        Test_ResponesBase::showMessage($result['code'] == 1, $result['mes']);
    }
}