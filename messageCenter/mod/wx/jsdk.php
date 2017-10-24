<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/10
 * Time: 9:54
 */
class Wx_Jsdk extends ProMessageCenter_MessageCheck
{
    public function __construct()
    {
        parent::__construct();
        param_request(array(
            'appId' => 'INT',
            'ip' => 'STRING',
            'notifyUrl' => 'STRING',
            'sign' => 'STRING'
        ), '', $this->param, array(
            'appId' => 0,
            'ip' => '',
            'notifyUrl' => '',
            'sign' => ''
        ));
    }


    public function indexFunc()
    {
        $_POST = $this->param;
        $rules = array(
            'appId|应用ID' => 'required|trim|callback_checkApp',
            'ip|ip' => 'trim',
            'notifyUrl|回调地址' => 'required|trim',
            'sign' => "required|trim|exact_length[32]|callback_checkSign"
        );
        $this->form->set_rules($rules);
        if ($this->form->run() === false) {
            show_message($this->form->error_string(), '', 2);
        }
        $info = $this->form->get_validation_data();
        $appId = $info['appId'];
        $notifyUrl = $info['notifyUrl'];
        $code = 1;
        try {
            $config = WxDrive_Config::createConfigByAppId($appId);
            $proMessageCenterWxJsdk = new ProMessageCenter_WxJsdk($config);
            $mes = $proMessageCenterWxJsdk->create($notifyUrl);
        } catch (Exception $e) {
            $code = $e->getCode();
            $mes = $e->getMessage();
        }
        show_message($mes, '', $code == 1);
    }

}