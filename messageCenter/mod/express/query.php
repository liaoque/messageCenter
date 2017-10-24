<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/31
 * Time: 16:53
 */
class Express_Query extends ProMessageCenter_MessageCheck
{
    public function __construct()
    {
        parent::__construct();
        param_request(array(
            'appId' => 'INT',
            'companyNum' => 'STRING',
            'waybill' => 'STRING',
            'ip' => 'STRING'
        ), '', $this->param, array(
            'appId' => 0,
            'company' => '',
            'waybill' => '',
            'ip' => ''
        ));
    }


    public function indexFunc()
    {
        $_POST = $this->param;
        $rules = array(
            'appId|应用ID' => 'is_numeric|trim',
            'companyNum|快递公司编号' => 'required|trim|callback_checkCompanyNum',
            'waybill|快递单号' => 'required|trim',
            'ip|ip' => 'required|trim'
        );
        $this->form->set_rules($rules);
        if ($this->form->run() === false) {
            show_message($this->form->error_string(), '', 2);
        }
        $info = $this->form->get_validation_data();
        $result = Model::factoryCreate('ProMessageCenter_KuaidiQuery')->query(
            $info['appId'],
            $info['companyNum'],
            $info['waybill']
        );
        show_message($result, '', 1);
    }
}