<?php
require_once ROOT . '/lib/ali/aop/SignData.php';
require_once ROOT . '/lib/ali/aop/AopClient.php';
require_once ROOT . '/lib/ali/aop/request/AlipayTradeCloseRequest.php';

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/16
 * Time: 17:38
 */
class PayDrive_AlipayClose extends PayDrive_AlipayPayBase implements PayDrive_InterfaceClose
{

    public function close(PayDrive_OrderData $order)
    {
        $sn = trim($order->getSn());
        $request = new AlipayTradeCloseRequest();
        $request->setBizContent(json_encode([
//            'product_code' => self::PRODUCT_CODE,
            'out_trade_no' => $sn,
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
        $response = $aop->Execute($request);
        if (empty($response) || empty($response->alipay_trade_query_response->code)) {
            throw new PayDrive_PayException(PayDrive_PayException::$mes[PayDrive_PayException::ERROR_REQUEST_PAY], PayDrive_PayException::ERROR_REQUEST_PAY);
        }

        $response = $response->alipay_trade_query_response;
        if ($response->code != 10000) {
            throw new PayDrive_PayException($response->code . '|' . $response->msg . '|' . $response->sub_msg, PayDrive_PayException::ERROR_REQUEST_PAY);
        }
        return true;
    }
}