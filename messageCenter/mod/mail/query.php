<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/27
 * Time: 10:14
 */
class Mail_query extends ProMessageCenter_MessageCheck
{
    public function __construct()
    {
        parent::__construct();
        param_request(array(
            'appId' => 'INT',
            'pkId' => 'STRING',
            'ip' => 'STRING',
            'sign' => 'STRING'
        ), '', $this->param, array(
            'appId' => 0,
            'pkId' => '',
            'ip' => '',
            'sign' => ''
        ));
    }


    public function indexFunc()
    {
        $_POST = $this->param;
        $rules = array(
            'appId|应用ID' => 'required|trim|callback_checkApp',
            'pkId|pkId' => 'required|trim',
            'sign' => "required|trim|exact_length[32]|callback_checkSign"
        );
        $this->form->set_rules($rules);
        if ($this->form->run() === false) {
            show_message($this->form->error_string(), '', 2);
        }
        $info = $this->form->get_validation_data();
        $result = DbMessageCenter_LogMail::getInstance()->find([
            'pkId' => $info['pkId']
        ], 'status');
        if (empty($result)) {
            show_message(DbMessageCenter_LogMail::$status[DbMessageCenter_LogPhone::STATUS_SEND_ING], '', 1);
        }
        show_message(DbMessageCenter_LogMail::$status[$result['status']], '', 1);

//        $message = new ProMessageCenter_Message();
//        $result = $message->setAppId($info['appId'])
//            ->setContent($info['content'])
//            ->setTarget($info['toMail'])
//            ->setTemplateId($info['templateId'])
//            ->send(Model::factoryCreate('ProMessageCenter_EmailQueue'));
//        if (!$result) {
//            show_message('邮件发送失败', '', 2);
//        }
//        show_message($result, '', 1);
    }
}