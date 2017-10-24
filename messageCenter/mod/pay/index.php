<?php

/**
 * 支付接口
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/8
 * Time: 10:57
 */
class Pay_Index extends ProMessageCenter_MessageCheck
{
    public function __construct()
    {
        parent::__construct();
        param_request(array(
            'appId' => 'INT',
            'num' => 'INT',
            'productSn' => 'STRING',
            'amount' => 'STRING',
            'ip' => 'STRING',
            'otherListId' => 'INT',
            'productName' => 'STRING',
            'desc' => 'STRING',
            'returnUrl' => 'STRING',
            'notifyUrl' => 'STRING',
            'timeOut' => 'STRING',
            'ext' => 'STRING',
            'sign' => 'STRING'
        ), '', $this->param, array(
            'appId' => 0,
            'num' => 0,
            'productSn' => '',
            'amount' => '',
            'ip' => '',
            'otherListId' => 0,
            'productName' => '',
            'desc' => '',
            'returnUrl' => '',
            'notifyUrl' => '',
            'timeOut' => '',
            'ext' => '',
            'sign' => ''
        ));
    }


    public function indexFunc()
    {
        $_POST = $this->param;
        $rules = array(
            'appId|应用ID' => 'required|trim|callback_checkApp',
            'num|数量' => 'required|trim|is_natural_no_zero',
            'productSn|订单号' => 'required|trim',
            'amount|金额(单位分)' => 'required|trim|is_natural',
            'ip|ip' => 'required|trim',
            'otherListId|支付应用id' => 'required|trim|is_natural_no_zero',
            'productName|商品名字' => 'required|trim',
            'desc|描述' => 'required|trim',
            'timeOut|有效期' => 'required|trim',
            'returnUrl|跳转地址' => 'trim|valid_url',
            'notifyUrl|回调地址' => 'required|trim|valid_url',
            'ext|扩展字段' => 'trim',
            'sign' => "requird|trim|exact_length[32]|callback_checkSign"
        );
        $this->form->set_rules($rules);
        if ($this->form->run() === false) {
            show_message($this->form->error_string(), '', 2);
        }
        $info = $this->form->get_validation_data();
        $code = 1;
        try {
            $mes = Model::factoryCreate('ProMessageCenter_PayPay')->pay($info);
        } catch (Exception $e) {
            $code = 2;
            $mes = $e->getMessage();
        }
        show_message($mes, '', $code);
    }
}