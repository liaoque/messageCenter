<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/9
 * Time: 17:15
 */
class ProMessageCenter_PayRefound
{

    private static $driveArray = [
        ProMessageCenter_PayPay::ALIPAY_SDK_PAY => 'PayDrive_AlipayRefund',
        ProMessageCenter_PayPay::ALIPAY_WAP_PAY => 'PayDrive_AlipayRefund',
        ProMessageCenter_PayPay::ALIPAY_WEB_PAY => 'PayDrive_AlipayRefund',
        ProMessageCenter_PayPay::WEIXIN_PAY_APP => 'PayDrive_WeiXinRefund',
        ProMessageCenter_PayPay::WEIXIN_PAY_JSDK => 'PayDrive_WeiXinRefund',
        ProMessageCenter_PayPay::WEIXIN_PAY_WEB => 'PayDrive_WeiXinRefund',
        ProMessageCenter_PayPay::HWY_SDK => false,
        ProMessageCenter_PayPay::HWY_WEB => false,
    ];


    /**
     * @param PayDrive_OrderData $orderData
     * @return mixed
     * @throws Exception
     */
    public function refund(PayDrive_OrderData $orderData)
    {
        $otherListId = $orderData->getOtherListId();
        $appId = $orderData->getAppId();
        $config = Model::factoryCreate('DbMessageCenter_PayOtherList')->getInfoByIdOfCache($otherListId);
        $resqustConfig = new PayDrive_resqustConfig($config);
        $className = self::$driveArray[$resqustConfig->getDriveType()];
        if ($className === false) {
            throw new Exception('该接口不支持退款', PayDrive_PayException::ERROR_SYS);
        }
        if (!class_exists($className)) {
            throw new Exception($className . '类不存在', PayDrive_PayException::ERROR_SYS);
        }
        $orderData->setRefundSn($orderData->createRefundSn());
        $drive = new $className($resqustConfig);
        $result = $drive->refund($orderData);
        $DbMessageCenter_PayRefound = Model::factoryCreate('DbMessageCenter_PayRefound');
        $DbMessageCenter_PayRefound->insert([
            'appId' => $appId,
            'orderId' => $orderData->getId(),
            'otherListId' => $otherListId,
            'refoundSn' => $orderData->getRefoundSn(),
            'refoundStatus' => DbMessageCenter_PayRefound::REFOUND_STATUS_ING,
            'refoundAmount' => $orderData->getRefoundAmount(),
            'refoundTotalAmount' => $result['refoundTotalAmount']
        ]);
        return $orderData;
    }


}