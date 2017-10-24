<?php

/**
 * 微信订单退款
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/14
 * Time: 20:35
 */
class PayDrive_WeiXinRefundQuery extends PayDrive_PayBase implements PayDrive_InterfaceRefoundQuery
{

    const REFOUND_STATUS_SUCESS = 'SUCCESS';
    const REFOUND_STATUS_ERROR = 'CHANGE';
    const REFOUND_STATUS_ING = 'PROCESSING';
    const REFOUND_STATUS_CLOSE = 'REFUNDCLOSE';

//SUCCESS—退款成功
//REFUNDCLOSE—退款关闭。
//PROCESSING—退款处理中
//CHANGE—退款异常，退款到银行发现用户的卡作废或者冻结了，导致原路退款银行卡失败，可前往商户平台（pay.weixin.qq.com）-交易中心，手动处理此笔退款。$n为下标，从0开始编号。
    public static $msg = [
        self::REFOUND_STATUS_SUCESS => DbMessageCenter_PayRefound::REFOUND_STATUS_SUCESS,
        self::REFOUND_STATUS_ERROR => DbMessageCenter_PayRefound::REFOUND_STATUS_ERROR,
        self::REFOUND_STATUS_ING => DbMessageCenter_PayRefound::REFOUND_STATUS_ING,
        self::REFOUND_STATUS_CLOSE => DbMessageCenter_PayRefound::REFOUND_STATUS_CLOSE,
    ];

    public function refundQuery(PayDrive_OrderData $order)
    {
        $sn = strtolower($order->getSn());
        $payData = new WxDrive_WeiXinPayData;
        $payData->setOutTradeNo($sn);
        $payData->setOutRefundNo($order->getRefundSn());
        $payData->setAppId($this->config->getPartnerAppId());
        $payData->setMchId($this->config->getPartner());
        $payData->setNonceStr(WxDrive_Base::createNonceStr());
        $payData->setKey($this->config->getAppKey());

        $WxPayOrder = new WxDrive_PayOrder();
        $response = $WxPayOrder->refundQuery($payData);
        if (empty($response)) {
            throw new PayDrive_PayException(PayDrive_PayException::$mes[PayDrive_PayException::ERROR_REQUEST_PAY], PayDrive_PayException::ERROR_REQUEST_PAY);
        }
        if ($response['return_code'] != 'SUCCESS') {
            throw new PayDrive_PayException($response['return_msg'], PayDrive_PayException::ERROR_REQUEST_PAY);
        }

        if ($response['result_code'] != 'SUCCESS') {
            throw new PayDrive_PayException($response['err_code'] . '|' . $response['err_code_des'], PayDrive_PayException::ERROR_REQUEST_PAY);
        }
        $data = [];
        for ($i = 0; $i < $response['refund_count']; $i++) {
            $data[] = [
                'refoundSn' => $response['out_refund_no_' . $i],
                'refoundStatus' => self::$mes[$response['refund_status_' . $i]],
            ];
        }

        return $data;
    }


}