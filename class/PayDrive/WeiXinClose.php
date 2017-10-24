<?php

/**
 * 微信订单查询接口
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/14
 * Time: 20:35
 */
class PayDrive_WeiXinClose extends PayDrive_PayBase implements PayDrive_InterfaceClose
{

    public function close(PayDrive_OrderData $order)
    {
        $sn = strtolower($order->getSn());
        $payData = new WxDrive_WeiXinPayData;
        $payData->setOutTradeNo($sn);
        $payData->setAppId($this->config->getPartnerAppId());
        $payData->setMchId($this->config->getPartner());
        $payData->setNonceStr(WxDrive_Base::createNonceStr());
        $payData->setKey($this->config->getAppKey());

        $WxPayOrder = new WxDrive_PayOrder;
        $response = $WxPayOrder->close($payData);

        if (empty($response)) {
            throw new PayDrive_PayException(PayDrive_PayException::$mes[PayDrive_PayException::ERROR_REQUEST_PAY], PayDrive_PayException::ERROR_REQUEST_PAY);
        }
        if ($response['return_code'] != 'SUCCESS') {
            throw new PayDrive_PayException($response['return_msg'], PayDrive_PayException::ERROR_REQUEST_PAY);
        }
        if ($response['result_code'] != 'SUCCESS') {
            throw new PayDrive_PayException($response['result_msg'], PayDrive_PayException::ERROR_REQUEST_PAY);
        }
        return true;
    }


}