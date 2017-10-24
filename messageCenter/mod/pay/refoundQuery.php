<?php

/**
 * 退款查询接口
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/8
 * Time: 10:57
 */
class Pay_RefoundQuery extends ProMessageCenter_MessageCheck
{
    public function __construct()
    {
        parent::__construct();
        param_request(array(
            'appId' => 'INT',
            'refundSn' => 'INT',
            'force' => 'INT',
            'ip' => 'STRING',
            'sign' => 'STRING'
        ), '', $this->param, array(
            'appId' => 0,
            'force' => 0,
            'refundSn' => '',
            'ip' => '',
            'sign' => ''
        ));
    }


    public function indexFunc()
    {
        $_POST = $this->param;
        $rules = array(
            'appId|应用ID' => 'required|trim|callback_checkApp',
            'refundSn|退款订单号' => 'required|trim|callback_checkSn',
            'force|强制' => 'trim|is_natural',
            'ip|ip' => 'required|trim',
            'sign' => "requird|trim|exact_length[32]|callback_checkSign"
        );
        $this->form->set_rules($rules);
        if ($this->form->run() === false) {
            show_message($this->form->error_string(), '', 2);
        }
        $info = $this->form->get_validation_data();
        $code = 1;
        try {
            $payRefound = Model::factoryCreate('DbMessageCenter_PayRefound');
            $mes = $payRefound->find(['refundSn' => $info['refundSn']], 'id, appId, sn, refundSn, otherListId');
            if (empty($mes)) {
                throw new PayDrive_PayException('退款订单不存在', PayDrive_PayException::ERROR_APP);
            } elseif ($mes['appId'] != $info['appId'] && !$info['force']) {
                throw new PayDrive_PayException('权限不足', PayDrive_PayException::ERROR_APP);
            }
            $orderData = new PayDrive_OrderData([
                'id' => $mes['id'],
                'appId' => $mes['appId'],
                'sn' => $mes['sn'],
                'otherListId' => $mes['otherListId'],
                'refundSn' => $mes['refundSn'],
            ]);

            $result = Model::factoryCreate('DbMessageCenter_PayRefound')->refundQuery($orderData);
            $mes = [
                'status' => DbMessageCenter_PayRefound::REFOUND_STATUS_ING,
                'mes' => '退款进行中'
            ];
            if ($result) {
                foreach ($result as $value) {
                    $refoundStatus = $value['refoundStatus'];
                    $refoundSn = $value['refoundSn'];
                    if ($refoundStatus == $info['refundSn']) {
                        $mes = [
                            'status' => $refoundStatus,
                            'mes' => DbMessageCenter_PayRefound::$status[$refoundStatus]
                        ];
                    }
                    $payRefound->update([
                        'refoundStatus' => $refoundStatus,
                    ], [
                        'refoundSn' => $refoundSn
                    ]);
                }
            }
        } catch (Exception $e) {
            $code = 2;
            $mes = $e->getMessage();
        }
        show_message($mes, '', $code);
    }
}