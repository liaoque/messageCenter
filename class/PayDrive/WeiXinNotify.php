<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/14
 * Time: 20:35
 */
class PayDrive_WeiXinNotify implements PayDrive_InterfaceNotify
{
    const NOTIFY_SUCESS_CODE = 'SUCCESS';
    const NOTIFY_ERROR_CODE = 'FAIL';

    const NOTIFY_SUCCESS_MSG = 'OK';


    public function notify($xml)
    {
        $xml = WxDrive_Base::parseXml($xml);
        $config = PhoneSite_WeiXinConfigList::configByAppId($xml['appid']);
        if (empty($config['appId'])) {
            return false;
        }

        $payData = new WxDrive_WeiXinPayData;
        $payData->setKey($config['key']);

        $WxPayOrder = new WxDrive_PayOrder;
        $result = $WxPayOrder->notify($payData, $xml);
//        $result['reCode'] = $this->sendResult($result['code'], $result['mes']);
        return $result;
    }

    static public function sendResult($code, $mes = null)
    {
        if ($code) {
            $code = self::NOTIFY_SUCESS_CODE;
            $mes = self::NOTIFY_SUCCESS_MSG;
        } else {
            $code = self::NOTIFY_ERROR_CODE;
        }
        return '<xml><return_code><![CDATA[' . $code . ']]></return_code>  <return_msg><![CDATA[' . $mes . ']]></return_msg></xml>';
    }


}