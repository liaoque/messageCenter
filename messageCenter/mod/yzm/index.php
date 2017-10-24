<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/27
 * Time: 9:08
 */
class Yzm_Index extends ProMessageCenter_MessageCheck
{

    public function indexFunc()
    {
        param_request(array(
            'appId' => 'INT',
            'ip' => 'STRING',
            'sign' => 'STRING'
        ), '', $this->param, array(
            'appId' => 0,
            'ip' => '',
            'sign' => ''
        ));
        $_POST = $this->param;
        $rules = array(
            'appId|应用ID' => 'required|trim|callback_checkApp',
            'ip|ip' => 'required|trim',
            'sign' => "required|trim|exact_length[32]|callback_checkSign"
        );
        $this->form->set_rules($rules);
        if ($this->form->run() === false) {
            show_message($this->form->error_string(), '', 2);
        }
        $info = $this->form->get_validation_data();
        $code = new ProMessageCenter_Code;
        $result = $code->makeCode($info['appId']);
        if (!$result) {
            show_message('创建验证码失败', '', 2);
        }
        show_message($result, '', 1);
    }

}