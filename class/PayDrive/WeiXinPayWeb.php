<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/14
 * Time: 20:08
 */
class PayDrive_WeiXinPayWeb extends PayDrive_PayBase implements PayDrive_InterfacePay
{

    public function createPayRequest(PayDrive_OrderData $order, $ext = [])
    {
//        $notify_url = $this->config->getNotifyUrl();
        $notify_url = $order->getNotifyUrl();
        $sn = strtolower($order->getSn());
        $amount = $order->getAmount();
        $productId = $order->getProductSn();
        //商品描述，可空
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


        ////IOS移动应用
//        {"h5_info": {"type":"IOS","app_name": "王者荣耀","bundle_id": "com.tencent.wzryIOS"}}

//安卓移动应用
//        {"h5_info": {"type":"Android","app_name": "王者荣耀","package_name": "com.tencent.tmgp.sgame"}}

//WAP网站应用
//        {"h5_info": {"type":"Wap","wap_url": "https://pay.qq.com","wap_name": "腾讯充值"}}
        if ($order['game_sub_type'] == 1) {
            $sceneInfo = [
                'h5_info' => [
                    'type' => 'IOS',
                    'app_name' => $order['app_name'],
                    'bundle_id' => $order['bundle_id'],
                ]
            ];
        } elseif ($order['type'] == 2) {
            $sceneInfo = [
                'h5_info' => [
                    'type' => 'Android',
                    'app_name' => $order['app_name'],
                    'package_name' => $order['package_name'],
                ]
            ];
        } else {
            $sceneInfo = [
                'h5_info' => [
                    'type' => 'h5_info',
                    'wap_url' => $order['wap_url'],
                    'wap_name' => $order['wap_name'],
                ]
            ];
        }
        $payData->setSceneInfo(Model::enCode($sceneInfo, true));


        $WxPayOrder = new WxDrive_PayOrder;
        return $WxPayOrder->JSDK($payData);
    }

}