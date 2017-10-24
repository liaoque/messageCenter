<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/31
 * Time: 16:17
 */
class Express_Subscribe extends ProMessageCenter_MessageCheck
{

    public function __construct()
    {
        parent::__construct();
        param_request(array(
            'appId' => 'INT',
            'companyNum' => 'STRING',
            'waybill' => 'STRING',
            'from' => 'STRING',
            'to' => 'STRING',
            'ip' => 'STRING',
            'sign' => 'STRING'
        ), '', $this->param, array(
            'appId' => 0,
            'company' => '',
            'waybill' => '',
            'from' => '',
            'to' => '',
            'ip' => '',
            'sign' => ''
        ));
    }


    public function indexFunc()
    {
        $_POST = $this->param;
        $rules = array(
            'appId|应用ID' => 'required|trim|callback_checkApp',
            'companyNum|快递公司编号' => 'required|trim|callback_checkCompanyNum',
            'waybill|快递单号' => 'required|trim',
            'from|出发城市' => 'trim',
            'to|目标城市' => 'trim',
            'ip|ip' => 'required|trim',
            'sign' => "requird|trim|exact_length[32]|callback_checkSign"
        );
        $this->form->set_rules($rules);
        if ($this->form->run() === false) {
            show_message($this->form->error_string(), '', 2);
        }
        $info = $this->form->get_validation_data();
        $info = [
            'appId' => $info['appId'],
            'companyNum' => $info['companyNum'],
            'companyListId' => $this->companyListId,
            'waybill' => $info['waybill'],
            'origin' => $info['from'],
            'target' => $info['to'],
            'ip' => $info['ip'],
        ];
        $result = Model::factoryCreate('ProMessageCenter_KuaiDiSubscribe')->subcribe($info);
        $msg = '失败';
        $code = 2;
        if ($result) {
            $msg = '成功';
            $code = 1;
        }
        show_message($msg, '', $code);
    }

}