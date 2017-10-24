<?php

/**
 * 微信订单退款
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/14
 * Time: 20:35
 */
class PayDrive_WeiXinRefund extends PayDrive_PayBase implements PayDrive_InterfaceRefound
{

    public function refund(PayDrive_OrderData $order)
    {
        $sn = strtolower($order->getSn());
        $payData = new WxDrive_WeiXinPayData;

//        退款金额
        $payData->setTotalFee($order->getAmount());
        //退款金额
        $payData->setRefundFee($order->getRefundAmount());
        //商户订单号
        $payData->setOutTradeNo($sn);
        //退款订单号
        $payData->setOutRefundNo($order->getRefundSn());
        $payData->setAppId($this->config->getPartnerAppId());
        $payData->setMchId($this->config->getPartner());
        $payData->setNonceStr(WxDrive_Base::createNonceStr());
        $payData->setKey($this->config->getAppKey());

        $WxPayOrder = new WxDrive_PayOrder;
        $response = $WxPayOrder->refund($payData);
        if (empty($response)) {
            throw new PayDrive_PayException(PayDrive_PayException::$mes[PayDrive_PayException::ERROR_REQUEST_PAY], PayDrive_PayException::ERROR_REQUEST_PAY);
        }
        if ($response['return_code'] != 'SUCCESS') {
            throw new PayDrive_PayException($response['return_msg'], PayDrive_PayException::ERROR_REQUEST_PAY);
        }
        if ($response['result_code'] != 'SUCCESS') {
            throw new PayDrive_PayException($response['err_code_des'], PayDrive_PayException::ERROR_REQUEST_PAY);
        }
        return [
            'refoundTotalAmount' => $response['refund_fee']
        ];
    }


}