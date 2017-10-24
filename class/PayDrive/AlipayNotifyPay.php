<?php
require_once ROOT . '/lib/ali/aop/AopClient.php';

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/16
 * Time: 17:38
 */
class PayDrive_AlipayNotifyPay extends PayDrive_AlipayPayBase implements PayDrive_InterfaceNotify
{
    const NOTIFY_SUCESS_CODE = 'success';
    const NOTIFY_ERROR_CODE = 'failure';

    /**
     * @param $data
     * @return mixed
     * @throws PayDrive_PayException
     *      sn  本地订单号
     *      order  支付接口订单号
     */
    public function notify($data)
    {
        $aop = new AopClient;
        $aop->alipayrsaPublicKey = $this->config->getPublicRSA2();
        $flag = $aop->rsaCheckV1($data, NULL, PayDrive_AlipayPayBase::SIGN_TYPE);
        if (!$flag) {
            throw new PayDrive_PayException(PayDrive_PayException::$mes[PayDrive_PayException::ERROR_OTHER_SIGN], PayDrive_PayException::ERROR_OTHER_SIGN);
        }
        if ($data['trade_status'] == 'TRADE_SUCCESS' || $data['trade_status'] == 'TRADE_FINISHED') {
            $result = [
                'sn' => $data['out_trade_no'],
                'order' => $data['trade_no'],
            ];
        } else {
            throw new PayDrive_PayException(PayDrive_PayException::$mes[PayDrive_PayException::ERROR_OTHER], PayDrive_PayException::ERROR_OTHER);
        }
        return $result;
    }


    public static function sendResult($code, $mes = null)
    {
        return $code == 1 ? self::NOTIFY_SUCESS_CODE : self::NOTIFY_ERROR_CODE;
    }

}