<?php

/**
 * 支付接口
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/8
 * Time: 10:57
 */
class Pay_TestIndex
{


    public function indexFunc()
    {

//        var_dump(ProMessageCenter_Request::payNotifyMessage(2, ''));
        $appId = '7';
        $productSn = '20171208121212';
        $productName = '快送大礼包';
        $productDesc = '礼包1: 1, 2, 3';
        $amount = '1000';
        $otherListId = '1';
        $key = '77777777';
        $num = 1;
        $timeOut = 30;
        $ext = [];
        $result = ProMessageCenter_Request::pay($appId, $productSn, $productName, $productDesc, $amount, $otherListId, $key, $notifyUrl = 'http://111.com', $returnUrl = 'http://111.com', $ext, $num, $timeOut);
        Test_ResponesBase::showMessage($result['code'] == 1, Model::enCode($result['mes']));

//        $appId = '7';
//        $productSn = '20171208132212124444';
//        $productName = '传奇至尊-游戏充值';
//        $productDesc = '礼包1: 1, 2, 3';
//        $amount = '1000';
//        $otherListId = '5';
//        $key = '77777777';
//        $num = 1;
//        $timeOut = 30;
//        $ext = [];
//        $result = ProMessageCenter_Request::pay($appId, $productSn, $productName, $productDesc, $amount, $otherListId, $key, $ext, $num, $timeOut);
//        Test_ResponesBase::showMessage($result['code'] == 1, Model::enCode($result['mes']));


//
//        $appId = '70';
//        $productSn = '20171208121212|1sad32d15as6d45a6|byugui';
//        $productName = '快送大礼包';
//        $productDesc = '礼包1: 1, 2, 3';
//        $amount = '1000';
//        $otherListId = '1';
//        $key = '77777777';
//        $num = 1;
//        $timeOut = 30;
//        $ext = [];
//        $result = ProMessageCenter_Request::pay($appId, $productSn, $productName, $productDesc, $amount, $otherListId, $key, $ext, $num, $timeOut);
//        Test_ResponesBase::showMessage($result['code'] == 1, Model::enCode($result['mes']));


    }


}