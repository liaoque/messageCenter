<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/9
 * Time: 17:15
 */
class ProMessageCenter_PayClose
{
    private static $driveArray = [
        ProMessageCenter_PayPay::ALIPAY_SDK_PAY => 'PayDrive_AlipayClose',
        ProMessageCenter_PayPay::ALIPAY_WAP_PAY => 'PayDrive_AlipayClose',
        ProMessageCenter_PayPay::ALIPAY_WEB_PAY => 'PayDrive_AlipayClose',
        ProMessageCenter_PayPay::WEIXIN_PAY_APP => 'PayDrive_WeiXinClose',
        ProMessageCenter_PayPay::WEIXIN_PAY_JSDK => 'PayDrive_WeiXinClose',
        ProMessageCenter_PayPay::WEIXIN_PAY_WEB => 'PayDrive_WeiXinClose',
        ProMessageCenter_PayPay::HWY_SDK => false,
        ProMessageCenter_PayPay::HWY_WEB => false,
    ];

//    private $drive;
    private $queue;
//    private $fileCache;

    const REDIS_KEY = 'promc:close';

    public function __construct()
    {
        $this->queue = Cache_Gather::getInstance();
//        $this->fileCache = Cache_File::getInstance();
//        $this->fileCache->setPath(self::getFileCachePath());
    }


    /**
     * 返回 真 关闭成功 假 关闭失败
     * @param $appId
     * @param $sn
     * @param $otherListId
     * @return boolean
     * @throws Exception
     */
    public function close($appId, $sn, $otherListId)
    {
        $orderData = new PayDrive_OrderData([
            'appId' => $appId,
            'sn' => $sn,
            'otherListId' => $otherListId
        ]);
        $config = Model::factoryCreate('DbMessageCenter_PayOtherList')->getInfoByIdOfCache($otherListId);
        $resqustConfig = new PayDrive_resqustConfig($config);
        $className = self::$driveArray[$resqustConfig->getDriveType()];
        if ($className === false) {
            $result = true;
        } else {
            if (!class_exists($className)) {
                throw new Exception($className . '类不存在', PayDrive_PayException::ERROR_SYS);
            }
            $drive = new $className($resqustConfig);
            $result = $drive->close($orderData);
        }
        if ($result) {
            /**
             * 订单存在直接关闭订单,
             * 不存在就加入队列
             */
            $payOrder = Model::factoryCreate('DbMessageCenter_PayOrder');
            $snInfo = $payOrder->getInfoBySnOfCache($sn);
            if (empty($snInfo)) {
                $result = $this->addQueue($sn);
            } else {
                $result = $payOrder->close($snInfo[$sn]);
            }
        }
        return $result;
    }


    public static function getFileCachePath()
    {
        return ROOT . '/' . TEMPLATE . '/var/payClose/';
    }

    public static function payQuqueKey($sn = null)
    {
//        return self::REDIS_KEY . 'pqk:' . substr(md5($sn), 0, 1);
//        短期不需要跑, 如果数据量大, 调整这个, 变成16个key, 并开启16个脚本跑
        return self::REDIS_KEY . ':pqk';
    }


    public function addQueue($sn)
    {
        /**
         * 检查关闭队列,如果有就删除
         */
        $notify = Model::factoryCreate('ProMessageCenter_PayNotify');
        if ($notify->inQueue($sn)) {
            throw new PayDrive_PayException('订单已付款成功,无法关闭订单', PayDrive_PayException::ERROR_APP);
        }
        $key = self::payQuqueKey($sn);
        if ($this->queue->set($key, $sn)) {
            throw new PayDrive_PayException('订单sn:' . $sn . '加入关闭队列设置失败', PayDrive_PayException::ERROR_SYS);
        }
        return true;
    }

    /**
     * 订单是否存在于缓存
     * @param $sn
     * @return mixed
     */
    public function inQueue($sn)
    {
        $key = self::payQuqueKey($sn);
        return $this->queue->exists($key, $sn);
    }

    /**
     * 从队列中删除订单
     * @param $sn
     */
    public function removeSnOfQueue($sn)
    {
        $key = self::payQuqueKey($sn);
        $this->queue->remove($key, $sn);
    }

    /**
     * 从队列中去数据
     * @param null $sn
     * @return mixed
     */
    public function getCloaseQueue($sn = null)
    {
        $key = self::payQuqueKey($sn);
        $data = $this->queue->getMemberByCount($key, 3000);
        return $data;
    }


}