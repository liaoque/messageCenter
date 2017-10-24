<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/27
 * Time: 9:15
 */
class Yzm_Check extends ProMessageCenter_MessageCheck
{
    public function indexFunc()
    {
        param_request(array(
            'appId' => 'INT',
            'pkId' => 'STRING',
            'code' => 'STRING',
            'ip' => 'STRING',
            'type' => 'int',
            'sign' => 'STRING'
        ), '', $this->param, array(
            'appId' => 0,
            'pkId' => '',
            'code' => '',
            'ip' => '',
            'type' => 0,
            'sign' => ''
        ));
        $_POST = $this->param;

        $rules = array(
            'appId|应用ID' => 'required|trim|callback_checkApp',
            'pkId|验证码id' => 'required|trim',
            'type|类型' => 'required|trim|is_natural_no_zero',
            'ip|ip' => 'required|trim',
            'code|验证码' => 'required|trim|callback_checkCode',
            'sign' => "required|trim|exact_length[32]|callback_checkSign"
        );

        $this->form->set_rules($rules);
        if ($this->form->run() === false) {
            show_message($this->form->error_string(), '', 2);
        }

        show_message('验证成功', '', 1);
    }

}