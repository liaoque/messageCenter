<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/9
 * Time: 17:15
 */
class ProMessageCenter_PayQuery
{
    private static $driveArray = [
        ProMessageCenter_PayPay::ALIPAY_SDK_PAY => 'PayDrive_AlipayQuery',
        ProMessageCenter_PayPay::ALIPAY_WAP_PAY => 'PayDrive_AlipayQuery',
        ProMessageCenter_PayPay::ALIPAY_WEB_PAY => 'PayDrive_AlipayQuery',
        ProMessageCenter_PayPay::WEIXIN_PAY_APP => 'PayDrive_WeiXinQuery',
        ProMessageCenter_PayPay::WEIXIN_PAY_JSDK => 'PayDrive_WeiXinQuery',
        ProMessageCenter_PayPay::WEIXIN_PAY_WEB => 'PayDrive_WeiXinQuery',
        ProMessageCenter_PayPay::HWY_SDK => 'PayDrive_HWySdk',
        ProMessageCenter_PayPay::HWY_WEB => 'PayDrive_HWyWeb',
    ];


    public function query($appId, $sn, $otherListId)
    {
        $orderData = new PayDrive_OrderData([
            'appId' => $appId,
            'sn' => $sn,
            'otherListId' => $otherListId
        ]);
        $config = Model::factoryCreate('DbMessageCenter_PayOtherList')->getInfoByIdOfCache($otherListId);
        $resqustConfig = new PayDrive_resqustConfig($config);
        $className = self::$driveArray[$resqustConfig->getDriveType()];
        if (!class_exists($className)) {
            throw new Exception($className . '类不存在', PayDrive_PayException::ERROR_SYS);
        }
        $drive = new $className($resqustConfig);
        $result = $drive->query($orderData);
        return $result;
    }

}