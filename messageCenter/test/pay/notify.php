<?php

/**
 * 回调接口
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/8
 * Time: 10:57
 */
class Pay_TestNotify extends ProMessageCenter_MessageCheck
{
    public function __construct()
    {
        parent::__construct();
        $this->data = file_get_contents('php://input');
    }

    public function ali()
    {
        $ali = new ProMessageCenter_PayNotify(0, ProMessageCenter_PayPay::ALIPAY_SDK_PAY);
        echo $ali->notify($this->data);
    }


    public function weixin()
    {
        $ali = new ProMessageCenter_PayNotify(0, ProMessageCenter_PayPay::WEIXIN_PAY_APP);
        echo $ali->notify($this->data);
    }

    public function hfb()
    {
        $ali = new ProMessageCenter_PayNotify(0, ProMessageCenter_PayPay::HWY_SDK);
        echo $ali->notify($this->data);
    }
}