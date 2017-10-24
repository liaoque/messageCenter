<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/28
 * Time: 19:21
 */
//汇付宝网银
class PayDrive_HNotify extends PayDrive_AlipayPayBase implements PayDrive_InterfaceNotify
{
    const NOTIFY_SUCESS_CODE = 'ok';
    const NOTIFY_ERROR_CODE = 'error';

    const QUERY_URL = 'https://query.heepay.com/Payment/Query.aspx';


    public function notify($data)
    {
//        result	必填	支付结果，1=成功 其它为未知
//        agent_id	必填	商户编号 如1234567
//        jnet_bill_no	必填	汇付宝交易号(订单号)
//        agent_bill_id	必填	商户系统内部的定单号
//        pay_type	必填	20
//        pay_amt	必填	订单实际支付金额(注意：此金额是用户的实付金额)
//        remark	必填	商家数据包，原样返回
//        pay_message	选填	支付结果信息，支付成功时为空
//        sign	必填	签名结果,与支付接口签名方式一致
        $result = $data['result'];
        $agent_id = $this->config->getPartner();
        $jnet_bill_no = $data['jnet_bill_no'];
        $agent_bill_id = $sn = $data['agent_bill_id'];
        $pay_type = $data['pay_type'];
        $pay_amt = $data['pay_amt'];
        $remark = $data['remark'];
        $key = $this->config->getAppKey();
        $returnSign = $data['sign'];

        $signStr = '';
        $signStr = $signStr . 'result=' . $result;
        $signStr = $signStr . '&agent_id=' . $agent_id;
        $signStr = $signStr . '&jnet_bill_no=' . $jnet_bill_no;
        $signStr = $signStr . '&agent_bill_id=' . $agent_bill_id;
        $signStr = $signStr . '&pay_type=' . $pay_type;
        $signStr = $signStr . '&pay_amt=' . $pay_amt;
        $signStr = $signStr . '&remark=' . $remark;
        $signStr = $signStr . '&key=' . $key;

        $sign = md5($signStr);
        if ($result != 1) {
            throw new PayDrive_PayException(PayDrive_PayException::$mes[PayDrive_PayException::ERROR_OTHER_MONEY], PayDrive_PayException::ERROR_OTHER_MONEY);
        } elseif (intval($pay_amt) < 0) {
            throw new PayDrive_PayException(PayDrive_PayException::$mes[PayDrive_PayException::ERROR_OTHER], PayDrive_PayException::ERROR_OTHER);
        } else if ($sign != $returnSign) {
            throw new PayDrive_PayException(PayDrive_PayException::$mes[PayDrive_PayException::ERROR_OTHER_SIGN], PayDrive_PayException::ERROR_OTHER_SIGN);
        }
        $sn = $agent_bill_id;
        $trade_no = $jnet_bill_no;
        $result = [
            'sn' => $sn,
            'order' => $trade_no
        ];
        return $result;
    }

    public static function sendResult($code, $mes = null)
    {
        return $code == 1 ? self::NOTIFY_SUCESS_CODE : self::NOTIFY_ERROR_CODE;
    }

}
