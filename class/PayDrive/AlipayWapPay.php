<?php
require_once ROOT . '/lib/ali/aop/request/AlipayTradeWapPayRequest.php';

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/16
 * Time: 11:33
 */
class PayDrive_AlipayWapPay extends PayDrive_AlipayPayBase implements PayDrive_InterfacePay
{

    public function createPayRequest(PayDrive_OrderData $order, $ext = [])
    {
        //商户订单号，商户网站订单系统中唯一订单号，必填
        $sn = trim($order->getSn());
        //订单名称，必填
        $subject = trim($order->getProductName($this->config->getTitle()));
        //付款金额，必填
        $amount = trim($order->getAmount());
        //商品描述，可空
        $body = trim($order->getDesc());
        // 服务器异步通知页面路径
//        $notify_url = $this->config->getNotifyUrl();
        $notify_url = $order->getNotifyUrl();
        // 页面跳转同步通知页面路径
//        $return_url = $this->config->getReturnUrl();
        $return_url = $order->getReturnUrl();

        $request = new AlipayTradeWapPayRequest();
        $request->setNotifyUrl($notify_url);
        $request->setReturnUrl($return_url);
        $request->setBizContent(json_encode([
            'product_code' => self::PRODUCT_CODE,
            'body' => $body,
            'subject' => $subject,
            'out_trade_no' => $sn,
            'timeout_express' => self::parseTime($order->getTimeOut()),
            'total_amount' => sprintf('%0.2f',$amount),
        ], JSON_UNESCAPED_UNICODE));

        $aop = new AopClient ();
        $aop->gatewayUrl = self::PAY_URL;
        $aop->appId = $this->config->getPartner();
        $aop->rsaPrivateKey = $this->config->getPrivateRSA2();
        $aop->alipayrsaPublicKey = $this->config->getPublicRSA2();
        $aop->apiVersion = self::VERSION;
        $aop->postCharset = self::INPUT_CHARSET;
        $aop->format = self::FORMAT;
        $aop->signType = self::SIGN_TYPE;
        // 开启页面信息输出
//        $aop->debugInfo = true;
        $result = $aop->pageExecute($request, "post");
        return $result;
    }

}