<?php

/**
 * 回调接口
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/8
 * Time: 10:57
 */
class Pay_Notify extends ProMessageCenter_MessageCheck
{
    public function __construct()
    {
        parent::__construct();
        $this->data = file_get_contents('php://input');
    }

    public function indexFunc()
    {
        param_request(array(
            'appId' => 'INT',
            'data' => 'STRING',
            'otherListId' => 'INT',
            'sign' => 'STRING'
        ), '', $_POST, array(
            'appId' => 0,
            'data' => '',
            'otherListId' => 0,
            'sign' => ''
        ));
        $rules = array(
            'appId|应用ID' => 'required|trim|callback_checkApp',
            'data|回调数据' => 'required|trim',
            'otherListId|支付应用id' => 'required|trim|is_natural_no_zero',
            'sign' => "requird|trim|exact_length[32]|callback_checkSign"
        );
        $this->form->set_rules($rules);
        if ($this->form->run() === false) {
            show_message($this->form->error_string(), '', 2);
        }
        $info = $this->form->get_validation_data();

        $otherListId = $info['otherListId'];
        $dbPayOtherList = Model::factoryCreate('DbMessageCenter_PayOtherList');
        $config = $dbPayOtherList->getInfoByIdOfCache($otherListId);
        $resqustConfig = new PayDrive_resqustConfig($config);
        $ali = new ProMessageCenter_PayNotify($info['appId'], $resqustConfig->getDriveType());
        $result = $ali->notify2($this->data);
        if ($result['code']) {
            show_message(1, '', $result['sn']);
        } else {
            show_message(2, '', $result['mes']);
        }
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