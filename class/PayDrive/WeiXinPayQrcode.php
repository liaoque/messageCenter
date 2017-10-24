<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/14
 * Time: 20:08
 */
class PayDrive_WeiXinPayQrcode extends PayDrive_PayBase implements PayDrive_InterfacePay
{

    public function createPayRequest(PayDrive_OrderData $order, $ext = [])
    {
//        $notify_url = $this->config->getNotifyUrl();
        $notify_url = $order->getNotifyUrl();
        $sn = strtolower($order->getSn());
        $amount = $order->getAmount();
        $productId = $order->getProductSn();
        $body = $order->getProductName($this->config->getTitle());

        //支付后返回的商户处理页面，URL参数是以http://或https://开头的完整URL地址(后台处理)
        $payData = new WxDrive_WeiXinPayData;
        $payData->setBody($body);
        $payData->setNotifyUrl($notify_url);
        $payData->setAppId($this->config->getPartnerAppId());
        $payData->setTimeExpire(date('YmdHis', time() + $order->getTimeOut()));
        $payData->setMchId($this->config->getPartner());
        $payData->setNonceStr(WxDrive_Base::createNonceStr());
        $payData->setKey($this->config->getAppKey());
        $payData->setOutTradeNo($sn);
        $payData->setTotalFee($amount);
        $payData->setProductId($productId);
//        var_dump($payData);exit();
        $WxPayOrder = new WxDrive_PayOrder;
        return $WxPayOrder->qrCode($payData);
    }


}