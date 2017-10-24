<?php


/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/14
 * Time: 20:08
 */
class PayDrive_WeiXinPayApp extends PayDrive_PayBase implements PayDrive_InterfacePay
{
    public function __construct(PayDrive_resqustConfig $resqustData)
    {
        $this->config = $resqustData;
    }

    public function createPayRequest(PayDrive_OrderData $order, $ext = [])
    {
//        $notify_url = $this->config->getNotifyUrl();
        $notify_url = $order->getNotifyUrl();
        $sn = strtolower($order->getSn());
        $amount = $order->getAmount();
        $appId = $this->config->getPartnerAppId();
        $title = $order->getProductName($this->config->getTitle());
        $mchId = $this->config->getPartner();
        $key = $this->config->getPartnerKey();
        /** 微信配置 END */
        //支付后返回的商户处理页面，URL参数是以http://或https://开头的完整URL地址(后台处理)
        $payData = new WxDrive_WeiXinPayData;
        $payData->setBody($title);
        $payData->setNotifyUrl($notify_url);
        $payData->setAppId($appId);
        $payData->setMchId($mchId);
        $payData->setNonceStr(WxDrive_Base::createNonceStr());
        $payData->setKey($key);
        $payData->setTimeExpire(date('YmdHis', time() + $order->getTimeOut() * 60));
        $payData->setOutTradeNo($sn);
        $payData->setTotalFee($amount);

        $WxPayOrder = new WxDrive_PayOrder;
        $result = $WxPayOrder->App($payData);
//        log_message('pay', json_encode($result), 'pay');
        return $result;
    }


    public function counteractKxd($uid, $gid, $kxd, $sn)
    {
        $phoneSiteKxd = new PhoneSite_Kxd();
        return $phoneSiteKxd->counteractKxd($uid, $gid, $kxd, $sn);
    }


}