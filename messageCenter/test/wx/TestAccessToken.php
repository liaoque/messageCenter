<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/10
 * Time: 9:54
 */
class Wx_TestAccessToken
{
    public function indexFunc()
    {
        $appId = '7';
        $key = '77777777';
        $result = ProMessageCenter_Request::wxAccessToken($appId, $key);
        Test_ResponesBase::showMessage($result['code'] == 1, $result['mes']);
    }

}