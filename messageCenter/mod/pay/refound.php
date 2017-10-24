<?php

/**
 * 退款接口
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/8
 * Time: 10:57
 */
class Pay_Refound extends ProMessageCenter_MessageCheck
{
    public function __construct()
    {
        parent::__construct();
        param_request(array(
            'appId' => 'INT',
            'sn' => 'STRING',
            'refundAmount' => 'STRING',
            'ip' => 'STRING',
            'refundDesc' => 'STRING',
            'sign' => 'STRING'
        ), '', $this->param, array(
            'appId' => 0,
            'sn' => '',
            'refundAmount' => '',
            'ip' => '',
            'refundDesc' => '',
            'sign' => ''
        ));
    }


    public function indexFunc()
    {
        $_POST = $this->param;
        $rules = array(
            'appId|应用ID' => 'required|trim|callback_checkApp',
            'sn|支付订单号' => 'required|trim|callback_checkSn',
            'refundAmount|金额(单位分)' => 'required|trim',
            'ip|ip' => 'required|trim',
            'refundDesc|描述' => 'required|trim',
            'sign' => "requird|trim|exact_length[32]|callback_checkSign"
        );
        $this->form->set_rules($rules);
        if ($this->form->run() === false) {
            show_message($this->form->error_string(), '', 2);
        }
        $info = $this->form->get_validation_data();
        $code = 1;
        $id = $this->payData['id'];
        try {
            $mes = Model::factoryCreate('DbMessageCenter_PayOrder')->getInfoByIdOfCache($id);
            if ($mes['appId'] != $info['appId'] && !$info['force']) {
                throw new PayDrive_PayException('权限不足', PayDrive_PayException::ERROR_AUTH);
            }
            $orderData = new PayDrive_OrderData([
                'id' => $mes['id'],
                'appId' => $mes['appId'],
                'sn' => $mes['sn'],
                'otherListId' => $mes['otherListId'],
                'amount' => $mes['aoumnt'],
                'refundAmount' => $info['refundAmount'],
                'refundDesc' => $info['refundDesc'],
            ]);

            $orderData = Model::factoryCreate('ProMessageCenter_PayRefound')->refund($orderData);
            $mes = $orderData->getRefundSn();
        } catch (Exception $e) {
            $code = 2;
            $mes = $e->getMessage();
        }
        show_message($mes, '', $code);
    }
}