<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/27
 * Time: 9:08
 */
class Yzm_mail extends ProMessageCenter_MessageCheck
{

    public function __construct()
    {
        parent::__construct();
        param_request(array(
            'appId' => 'INT',
            'templateId' => 'STRING',
            'toMail' => 'STRING',
            'ip' => 'STRING',
            'sign' => 'STRING'
        ), '', $this->param, array(
            'appId' => 0,
            'templateId' => '',
            'toMail' => '',
            'ip' => '',
            'sign' => ''
        ));
    }


    public function indexFunc()
    {
        $_POST = $this->param;
        $rules = array(
            'appId|应用ID' => 'required|trim|callback_checkApp',
            'templateId|模板ID' => 'required|trim|callback_checkTemplate',
            'toMail|邮件发送地址' => 'required|trim',
            'ip|ip' => 'required|trim',
            'sign' => "required|trim|exact_length[32]|callback_checkSign"
        );
        $this->form->set_rules($rules);
        if ($this->form->run() === false) {
            show_message($this->form->error_string(), '', 2);
        }
        $info = $this->form->get_validation_data();
        $message = new ProMessageCenter_Message();
        $message->setAppId($info['appId'])
            ->setTemplateId($this->templateId)
            ->setTarget($info['toMail']);
        $code = new ProMessageCenter_Code;
        $result = $code->sendEmailCode($message);
        if (!$result) {
            show_message('创建验证码失败', '', 2);
        }
        show_message($result, '', 1);
    }

}