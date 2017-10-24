<?php
if (!defined('IN_BOOT')) {
    exit('Access Denied');
}

class Phone_Index extends ProMessageCenter_MessageCheck
{
    public function __construct()
    {
        parent::__construct();
        $_POST = Model::deCode(file_get_contents('php://input'));
        param_request(array(
            'appId' => 'INT',
            'templateId' => 'INT',
            'phone' => 'STRING',
            'content' => 'STRING',
            'ip' => 'STRING',
            'sign' => 'STRING'
        ), '', $this->param, array(
            'appId' => 0,
            'templateId' => 0,
            'phone' => '',
            'content' => '',
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
            'phone|手机号' => 'required|trim',
            'content|内容' => 'required|trim',
            'ip|ip' => 'trim',
            'sign' => "required|trim|exact_length[32]|callback_checkSign"
        );
        $this->form->set_rules($rules);
        if ($this->form->run() === false) {
            show_message($this->form->error_string(), '', 2);
        }
        $info = $this->form->get_validation_data();
        $info['content'] = Model::deCode($info['content']);
        $message = new ProMessageCenter_Message();
        $result = $message->setAppId($info['appId'])
            ->setContent($info['content'])
            ->setTarget($info['phone'])
            ->setTemplateId($this->templateId)
            ->send(Model::factoryCreate('ProMessageCenter_SmsQueue'));
        if (!$result) {
            show_message('短信发送失败', '', 2);
        }
        show_message($result, '', 1);
    }
}

