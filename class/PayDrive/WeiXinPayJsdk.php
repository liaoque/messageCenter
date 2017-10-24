<?php

/**
 * JSDK
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/14
 * Time: 20:08
 */
class PayDrive_WeiXinPayJsdk extends PayDrive_PayBase implements PayDrive_InterfacePay
{

    public function createPayRequest(PayDrive_OrderData $order, $ext = [])
    {
//        $notify_url = $this->config->getNotifyUrl();
        $notify_url = $order->getNotifyUrl();
        $sn = strtolower($order->getSn());
        $amount = $order->getAmount();
        $appId = $this->config->getPartnerAppId();
        $title = $order->getProductName($this->config->getTitle());
        $mchId = $this->config->getPartner();
        $key = $this->config->getAppKey();
        $openId = $ext['openId'];

        //支付后返回的商户处理页面，URL参数是以http://或https://开头的完整URL地址(后台处理)
        $payData = new WxDrive_WeiXinPayData;
        $payData->setBody($title);
        $payData->setNotifyUrl($notify_url);
        $payData->setAppId($appId);
        $payData->setMchId($mchId);
        $payData->setTimeExpire(date('YmdHis', time() + $order->getTimeOut()));
        $payData->setNonceStr(WxDrive_Base::createNonceStr());
        $payData->setKey($key);
        $payData->setOutTradeNo($sn);
        $payData->setTotalFee($amount);
        $payData->setOpenId($openId);

        $WxPayOrder = new WxDrive_PayOrder;
        return $WxPayOrder->JSDK($payData);
    }

}