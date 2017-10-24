<?php
require_once ROOT . '/lib/ali/aop/AopClient.php';
require_once ROOT . '/lib/ali/aop/request/AlipayTradeFastpayRefundQueryRequest.php';

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/16
 * Time: 17:38
 */
class PayDrive_AlipayRefundQuery extends PayDrive_AlipayPayBase
{
    public function refundQuery(PayDrive_OrderData $order)
    {
        $sn = trim($order->getSn());
        //标识一次退款请求，同一笔交易多次退款需要保证唯一，如需部分退款，则此参数必传
        $out_request_no = trim($order->getRefundUnique());
        $request = new AlipayTradeFastpayRefundQueryRequest();
        $request->setBizContent(json_encode([
            'out_trade_no' => $sn,
            'out_request_no' => $out_request_no,
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

        $data = [];
        if (!empty($response->out_request_no) && $response->out_request_no == $out_request_no) {
            $data[] = [
                'refoundSn' => $out_request_no,
                'refoundStatus' => ProMessageCenter_PayRefound::REFOUND_STATUS_SUCESS
            ];
        }
        return $data;
    }

}