<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/27
 * Time: 9:15
 */
class Yzm_TestCheck
{
    public function indexFunc()
    {
        $appId = '7';
        $key = '77777777';
        $result = ProMessageCenter_Request::yzmCheck($appId, $key, '38', '6093');
        Test_ResponesBase::showMessage($result['code'] == 1, $result['mes']);
    }

}