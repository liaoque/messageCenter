<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/9
 * Time: 17:14
 */
class ProMessageCenter_PayPay
{
    const ALIPAY_SDK_PAY = 1;
    const ALIPAY_WAP_PAY = 2;
    const ALIPAY_WEB_PAY = 3;
    const WEIXIN_PAY_APP = 4;
    const WEIXIN_PAY_JSDK = 5;
    const WEIXIN_PAY_WEB = 6;
    const WEIXIN_PAY_H5 = 9;
    const HWY_SDK = 7;
    const HWY_WEB = 8;
    private $queue;
    private $fileCache;

    const REDIS_KEY = 'promc:pay';

    private static $driveArray = [
        self::ALIPAY_SDK_PAY => 'PayDrive_AlipaySdkPay',
        self::ALIPAY_WAP_PAY => 'PayDrive_AlipayWapPay',
        self::ALIPAY_WEB_PAY => 'PayDrive_AlipayWebPay',
        self::WEIXIN_PAY_APP => 'PayDrive_WeiXinPayApp',
        self::WEIXIN_PAY_JSDK => 'PayDrive_WeiXinPayJsdk',
        self::WEIXIN_PAY_WEB => 'PayDrive_WeiXinPayQrcode',
        self::WEIXIN_PAY_H5 => 'PayDrive_WeiXinPayWeb',
        self::HWY_SDK => 'PayDrive_HWySdk',
        self::HWY_WEB => 'PayDrive_HWyWeb',
    ];

    public static $driveType = [
        self::ALIPAY_SDK_PAY => '支付宝sdk',
        self::ALIPAY_WAP_PAY => '支付宝移动端',
        self::ALIPAY_WEB_PAY => '支付宝网页',
        self::WEIXIN_PAY_APP => '微信app',
        self::WEIXIN_PAY_JSDK => '微信jsdk',
        self::WEIXIN_PAY_WEB => '微信网页',
        self::HWY_SDK => '汇付宝网银sdk',
        self::HWY_WEB => '汇付宝网页版'
    ];

    public function __construct()
    {
        $this->queue = Cache_SortGather::getInstance();
        $this->fileCache = new Cache_File;
        $this->fileCache->setPath(self::getFileCachePath());
    }

    public function getQueue()
    {
        return $this->queue;
    }

    public static function payQuequeKey($sn = null)
    {
//        return self::REDIS_KEY . 'pqk:' . substr(md5($sn), 0, 1);
//        短期不需要跑, 如果数据量大, 调整这个, 变成16个key, 并开启16个脚本跑
        return self::REDIS_KEY . ':pqk';
    }


    public static function payQuequePkKey()
    {
//        return self::REDIS_KEY . 'pqk:' . substr(md5($sn), 0, 1);
//        短期不需要跑, 如果数据量大, 调整这个, 变成16个key, 并开启16个脚本跑
        return self::REDIS_KEY . ':pqk:pkk';
    }

    /**
     * 获取充值订单数据
     * @param null $sn
     * @return mixed
     */
    public function getPayQueue($sn = null)
    {
        $key = self::payQuequeKey($sn);
        $data = $this->queue->getByIndex($key, 0, 3000, 'asc', true);
        return $data;
    }

    /**
     * 删除充值集合中的订单, 并清除文件缓存
     * @param $sn
     */
    public function removeSnOfQueue($sn)
    {
        $key = self::payQuequeKey($sn);
        $this->queue->remove($key, [$sn]);
        $this->fileCache->del($sn);
    }

    /**
     * 从文件中获取关于队列缓存的数据
     * @param $sn
     * @return string
     */
    public function getFileCacheOfQueue($sn)
    {
        return $this->fileCache->get($sn);
    }

    /**
     * 设置主键
     * @return int
     */
    public function getIncPkId()
    {
        return PhpRedis::getInstance()->incr(self::payQuequePkKey());
    }

    public static function getFileCachePath()
    {
        return ROOT . '/' . TEMPLATE . '/var/payPay';
    }

    public function addQueue(PayDrive_OrderData $orderData)
    {
//        $dbPayOrderList = Model::factoryCreate('DbMessageCenter_PayOtherList');
//        $result = $dbPayOrderList->insert([
//            'id' => $orderData->getId(),
//            'appId' => $orderData->getAppId(),
//            'otherListId' => $orderData->getOtherListId(),
//            'sn' => $orderData->getSn(),
//            'aoumnt' => $orderData->getAmount(),
//            'productName' => $orderData->getProductName(),
//            'productSn' => $orderData->getProductSn(),
//            'productDesc' => $orderData->getDesc(),
//            'timeOut' => $orderData->getTimeOut(),
//            'num' => $orderData->getNum(),
//            'createTime' => $orderData->getCreateTime(),
//            'ip' => $orderData->getIp(),
//            'status' => $orderData->getStatus(),
//        ]);
//        if (!$result) {
//            throw new Exception('数据库插入失败', PayDrive_PayException::ERROR_SYS);
//        }


        $sn = $orderData->getSn();
        $data = [
            'id' => $orderData->getId(),
            'appId' => $orderData->getAppId(),
            'otherListId' => $orderData->getOtherListId(),
            'sn' => $sn,
            'aoumnt' => $orderData->getAmount(),
            'productName' => $orderData->getProductName(),
            'productSn' => $orderData->getProductSn(),
            'productDesc' => $orderData->getDesc(),
            'timeOut' => $orderData->getTimeOut(),
            'num' => $orderData->getNum(),
            'createTime' => $orderData->getCreateTime(),
            'ip' => $orderData->getIp(),
            'status' => DbMessageCenter_PayOrder::STATUS_PAY_ING,
        ];

        if (!$this->fileCache->set($sn, $data)) {
            throw new PayDrive_PayException('文件缓存设置失败: ' . Model::enCode($data, true), PayDrive_PayException::ERROR_SYS);
        }
        $key = self::payQuequeKey($sn);
        if (!$this->queue->set($key, [$sn => $data['id']])) {
            throw new PayDrive_PayException('加入队列设置失败: ' . Model::enCode($data), PayDrive_PayException::ERROR_SYS);
        }
        return true;
    }

    public function pay($info)
    {

        $otherListId = $info['otherListId'];
        $dbPayOtherList = Model::factoryCreate('DbMessageCenter_PayOtherList');
        $config = $dbPayOtherList->getInfoByIdOfCache($otherListId);
        $resqustConfig = new PayDrive_resqustConfig($config);

        $className = self::$driveArray[$resqustConfig->getDriveType()];
        if (!class_exists($className)) {
            throw new Exception($className . '类不存在', PayDrive_PayException::ERROR_SYS);
        }

        $drive = new $className($resqustConfig);
        $orderData = new PayDrive_OrderData($info);
        $id = $this->getIncPkId();
        $orderData->setId($id);
        $orderData->setSn($orderData->createOrderSn($id));
        $this->addQueue($orderData);
        $requsetData = $drive->createPayRequest($orderData);
        return [
            'sn' => $orderData->getSn(),
            'requsetData' => $requsetData
        ];
    }

}