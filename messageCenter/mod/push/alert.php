<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/13
 * Time: 17:07
 */
class Push_Alert extends ProMessageCenter_MessageCheck
{
    public function __construct()
    {
        parent::__construct();
        $_POST = Model::deCode(file_get_contents('php://input'));

        param_request(array(
            'appId' => 'INT',
            'templateId' => 'INT',
            'content' => 'STRING',
            'target' => 'STRING',
            'ip' => 'STRING',
            'sign' => 'STRING'
        ), '', $this->param, array(
            'appId' => 0,
            'templateId' => 0,
            'content' => '',
            'target' => '',
            'ip' => '',
            'sign' => ''
        ));
    }

    public function validation($type)
    {
        $_POST = $this->param;
        $rules = array(
            'appId|应用ID' => 'required|trim|callback_checkApp',
            'templateId|模版id' => 'required|trim|callback_checkTemplate',
            'content|内容' => 'required|trim',
            'target|目标' => $type == 'one' ? 'required|trim' : 'trim',
            'ip|ip' => 'required|trim',
            'sign' => "required|trim|exact_length[32]|callback_checkSign"
        );
        $this->form->set_rules($rules);
        if ($this->form->run() === false) {
            show_message($this->form->error_string(), '', 2);
        }
        return $this->form->get_validation_data();
    }


    public function oneFunc()
    {
        $info = $this->validation('one');
        $info['extras'] = $info['content'];
        $info['templateId'] = $this->templateId;
        unset($info['content']);
        $code = 1;
        $mes = 'ok';
        try {
            $targets = explode('|', $info['target']);
            foreach ($targets as $value) {
                $info['target'] = $value;
                $proMessageCenterJPushData = new ProMessageCenter_PushData($info);
                $proMessageCenterJPushData->validation();
                if (!$proMessageCenterJPushData->send()) {
                    $code = 2;
                    $mes = '发送失败';
                }
            }
        } catch (Exception $e) {
            $code = $e->getCode();
            $mes = $e->getMessage();
        }
        show_message($mes, '', $code == 1);
    }


    public function allFunc()
    {
        $info = $this->validation('all');
        $info['extras'] = $info['content'];
        $info['templateId'] = $this->templateId;
        unset($info['content']);
        $code = 1;
        $mes = 'ok';
        try {
            $proMessageCenterJPushData = new ProMessageCenter_PushData($info);
            $proMessageCenterJPushData->validation();
            if (!$proMessageCenterJPushData->send()) {
                $code = 2;
                $mes = '发送失败';
            }
        } catch (Exception $e) {
            $code = $e->getCode();
            $mes = $e->getMessage();
        }
        show_message($mes, '', $code == 1);
    }

}