<?php

/**
 * 关闭支付接口
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/8
 * Time: 10:57
 */
class Pay_Close extends ProMessageCenter_MessageCheck
{
    public function __construct()
    {
        parent::__construct();
        param_request(array(
            'appId' => 'INT',
            'sn' => 'STRING',
            'force' => 'INT',
            'ip' => 'STRING',
            'sign' => 'STRING'
        ), '', $this->param, array(
            'appId' => 0,
            'sn' => '',
            'force' => 0,
            'ip' => '',
            'sign' => ''
        ));
    }


    public function indexFunc()
    {
        $_POST = $this->param;
        $rules = array(
            'appId|应用ID' => 'required|trim|callback_checkApp',
            'sn|支付订单号' => 'required|trim|callback_checkSn',
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
        $id = $this->payData['id'];
        try {
            $mes = Model::factoryCreate('DbMessageCenter_PayOrder')->getInfoByIdOfCache($id);
            if (($mes['appId'] != $info['appId'] && !$info['force'])) {
                throw new PayDrive_PayException('权限不足', PayDrive_PayException::ERROR_AUTH);
            }

            $result = Model::factoryCreate('ProMessageCenter_PayClose')->close(
                $mes['appId'],
                $mes['sn'],
                $mes['otherListId']
            );
            $mes = '失败';
            if (!$result) {
                Model::factoryCreate('DbMessageCenter_PayOrder')->update([
                    'status' => DbMessageCenter_PayOrder::STATUS_PAY_CLOSE
                ], [
                    'id' => $id
                ]);
                $mes = '成功';
            }


        } catch (Exception $e) {
            $code = 2;
            $mes = $e->getMessage();
        }
        show_message($mes, '', $code);
    }
}