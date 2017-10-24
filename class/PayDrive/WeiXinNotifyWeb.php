<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/14
 * Time: 20:35
 */
class PayDrive_WeiXinNotifyWeb implements PayDrive_InterfaceNotify
{
    public function notify($xml)
    {
        $xml = WxDrive_Base::parseXml($xml);
        $payData = new WxDrive_WeiXinPayData;
        $payData->setKey(PhoneSite_WeiXinPayQrcode::KEY);
        $WxPayOrder = new WxDrive_PayOrder;
        $result = $WxPayOrder->notify($payData, $xml);
//        $result['reCode'] = $this->sendResult($result['code'], $result['mes']);
        return $result;
    }

    static public function sendResult($code, $mes = null)
    {
        if ($code) {
            $code = PayDrive_WeiXinNotify::NOTIFY_SUCESS_CODE;
            $mes = PayDrive_WeiXinNotify::NOTIFY_SUCCESS_MSG;
        } else {
            $code = PayDrive_WeiXinNotify::NOTIFY_ERROR_CODE;
        }
        return '<xml><return_code><![CDATA[' . $code . ']]></return_code>  <return_msg><![CDATA[' . $mes . ']]></return_msg></xml>';
    }


}