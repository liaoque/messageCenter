<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/9
 * Time: 17:16
 */
class ProMessageCenter_PayRefoundQuery
{
    private static $driveArray = [
        ProMessageCenter_PayPay::ALIPAY_SDK_PAY => 'PayDrive_AlipayRefundQuery',
        ProMessageCenter_PayPay::ALIPAY_WAP_PAY => 'PayDrive_AlipayRefundQuery',
        ProMessageCenter_PayPay::ALIPAY_WEB_PAY => 'PayDrive_AlipayRefundQuery',
        ProMessageCenter_PayPay::WEIXIN_PAY_APP => 'PayDrive_WeiXinRefundQuery',
        ProMessageCenter_PayPay::WEIXIN_PAY_JSDK => 'PayDrive_WeiXinRefundQuery',
        ProMessageCenter_PayPay::WEIXIN_PAY_WEB => 'PayDrive_WeiXinRefundQuery',
        ProMessageCenter_PayPay::HWY_SDK => false,
        ProMessageCenter_PayPay::HWY_WEB => false,
    ];

    public function refundQuery(PayDrive_OrderData $orderData)
    {
        $otherListId = $orderData->getOtherListId();

        $config = Model::factoryCreate('DbMessageCenter_PayOtherList')->getInfoByIdOfCache($otherListId);
        $resqustConfig = new PayDrive_resqustConfig($config);
        $className = self::$driveArray[$resqustConfig->getDriveType()];
        if ($className === false) {
            return true;
        }
        if (!class_exists($className)) {
            throw new Exception($className . '类不存在', PayDrive_PayException::ERROR_SYS);
        }
        $drive = new $className($resqustConfig);
        $result = $drive->refundQuery($orderData);
        return $result;
    }

}