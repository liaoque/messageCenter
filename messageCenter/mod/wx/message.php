<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/11
 * Time: 16:06
 */

class Wx_Message extends ProMessageCenter_MessageCheck
{

    public function __construct()
    {
        parent::__construct();
        $_POST = Model::deCode(file_get_contents('php://input'));
        param_request(array(
            'appId' => 'INT',
            'openId' => 'STRING',
            'templateId' => 'INT',
            'content' => 'STRING',
            'ext' => 'STRING',
            'ip' => 'STRING',
            'sign' => 'STRING'
        ), '', $this->param, array(
            'appId' => 0,
            'openId' => '',
            'templateId' => 0,
            'content' => '',
            'ext' => '',
            'ip' => '',
            'sign' => ''
        ));

    }

    public function validation($type)
    {
        $_POST = $this->param;
        if ($type == 'one') {
            $rules = array(
                'appId|应用ID' => 'required|trim|callback_checkApp',
                'openId|openId' => 'required|trim',
                'templateId|模版id' => 'required|trim|callback_checkTemplate',
                'content|内容' => 'required|trim',
                'ext|扩展数据' => 'required|trim',
                'ip|ip' => 'trim',
                'sign' => "required|trim|exact_length[32]|callback_checkSign"
            );
        } else {
            $rules = array(
                'appId|应用ID' => 'required|trim|callback_checkApp',
                'content|内容' => 'required|trim',
                'ip|ip' => 'trim',
                'sign' => "required|trim|exact_length[32]|callback_checkSign"
            );
        }
        $this->form->set_rules($rules);
        if ($this->form->run() === false) {
            show_message($this->form->error_string(), '', 2);
        }
        return $this->form->get_validation_data();
    }


    public function oneFunc()
    {
        $info = $this->validation('one');
        $code = 1;
        $mes = 'ok';
        $info['templateId'] = $this->templateId;
        $info['type'] = DbMessageCenter_LogWxTemplate::TYPE_ONE;
        try {
//            $config = WxDrive_Config::createConfigByAppId($appId);
//            $proMessageCenterWxTemplate = new ProMessageCenter_WxTemplate($config);
//            $proMessageCenterWxTemplate->validation($info['templateId']);
            $targets = explode('|', $info['openId']);
            foreach ($targets as $value) {
                $info['openId'] = $value;
                $info['ext'] = Model::deCode($info['ext']);
                $proMessageCenterWxTemplateData = new ProMessageCenter_WxTemplateData($info);
                $proMessageCenterWxTemplateData->validation();
                if (!$proMessageCenterWxTemplateData->send()) {
                    $code = 2;
                    $mes = '发送失败';
                }
            }
        } catch (Exception $e) {
            $code = $e->getCode();
            $mes = $e->getMessage();
        }
        show_message($mes, '', $code);
    }

    public function allFunc()
    {
        $info = $this->validation('all');
        $code = 1;
        $mes = 'ok';
        unset($info['sign']);
        try {
            $info['type'] = DbMessageCenter_LogWxTemplate::TYPE_ALL;
            $info['templateId'] = $this->templateId;
            $proMessageCenterWxTemplateData = new ProMessageCenter_WxTemplateData($info);
//            $proMessageCenterWxTemplateData->validation();
            if (!$proMessageCenterWxTemplateData->send()) {
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