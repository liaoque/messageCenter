<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/9
 * Time: 17:14
 */
class ProMessageCenter_PayNotify
{
    private static $driveArray = [
        ProMessageCenter_PayPay::ALIPAY_SDK_PAY => 'PayDrive_AlipayNotifyPay',
        ProMessageCenter_PayPay::ALIPAY_WAP_PAY => 'PayDrive_AlipayNotifyPay',
        ProMessageCenter_PayPay::ALIPAY_WEB_PAY => 'PayDrive_AlipayNotifyPay',
        ProMessageCenter_PayPay::WEIXIN_PAY_APP => 'PayDrive_WeiXinNotify',
        ProMessageCenter_PayPay::WEIXIN_PAY_JSDK => 'PayDrive_WeiXinNotify',
        ProMessageCenter_PayPay::WEIXIN_PAY_WEB => 'PayDrive_WeiXinNotify',
        ProMessageCenter_PayPay::HWY_SDK => 'PayDrive_HNotify',
        ProMessageCenter_PayPay::HWY_WEB => 'PayDrive_HNotify',
    ];

    private $drive;
    private $queue;
    private $fileCache;

    const REDIS_KEY = 'promc:notify';

    public function __construct($appId = null, $otherListId = null)
    {
        if ($appId) {
            $config = Model::factoryCreate('DbMessageCenter_PayOtherList')->getInfoByIdOfCache($otherListId);
            $resqustConfig = new PayDrive_resqustConfig($config);
            $className = self::$driveArray[$resqustConfig->getDriveType()];
            if (!class_exists($className)) {
                throw new Exception($className . '类不存在', PayDrive_PayException::ERROR_SYS);
            }
            $this->drive = new $className($resqustConfig);
        }
        $this->queue = Cache_Gather::getInstance();
        $this->fileCache = new Cache_File;
        $this->fileCache->setPath(self::getFileCachePath());
    }

    public function notify($data)
    {
        $code = 1;
        $mes = null;
        try {
            $result = $this->drive->notify($data);
            $this->addQueue($result['sn'], $result['order']);
        } catch (Exception $e) {
            $code = false;
            $mes = $e->getMessage();
        }
        return $this->sendResult($code, $mes);
    }

    public static function notifyMes($code, $mes, $type = ProMessageCenter_PayPay::ALIPAY_SDK_PAY)
    {
        if (empty(self::$driveArray[$type])) {
            throw new Exception('支付类型不存在', '', -500);
        }
        return call_user_func_array([
            self::$driveArray[$type],
            'sendResult'
        ], [
            $code, $mes
        ]);
    }

    public function notify2($data)
    {
        $code = 1;
        $mes = null;
        $sn = '';
        try {
            $result = $this->drive->notify($data);
            $sn = $result['sn'];
            $this->addQueue($sn, $result['order']);
        } catch (Exception $e) {
            $code = false;
            $mes = $e->getMessage();
        }
        return [
            'code' => $code,
            'sn' => $sn,
            'mes' => $mes
        ];
    }

    public function sendResult($code, $mes = null)
    {
        $result = $this->drive->sendResult($code, $mes);
        return $result;
    }

    public static function getFileCachePath()
    {
        return ROOT . '/' . TEMPLATE . '/var/payNotify/';
    }

    public static function payQuqueKey($sn = null)
    {
//        return self::REDIS_KEY . 'pqk:' . substr(md5($sn), 0, 1);
//        短期不需要跑, 如果数据量大, 调整这个, 变成16个key, 并开启16个脚本跑
        return self::REDIS_KEY . ':pqk';
    }

    public function addQueue($sn, $otherSn)
    {
        /**
         * 检查关闭队列,如果有就删除
         */
        $close = Model::factoryCreate('ProMessageCenter_PayClose');
        if ($close->inQueue($sn)) {
            $close->remove($sn);
        }
        /**
         * 设置文件缓存, 并加入队列
         */

        $data = [
            'otherSn' => $otherSn,
            'status' => DbMessageCenter_PayOrder::STATUS_PAY_SUCCESS
        ];
        if (!$this->fileCache->set($sn, $data)) {
            throw new PayDrive_PayException("订单:$sn 设置文件缓存失败: data:" . Model::enCode($data), PayDrive_PayException::ERROR_SYS);
        }

        $key = self::payQuqueKey($sn);
        if ($this->queue->set($key, $sn)) {
            throw new PayDrive_PayException("订单:$sn 加入队列失败", PayDrive_PayException::ERROR_SYS);
        }
        return true;
    }

    /**
     * 从文件中获取关于队列缓存的数据
     * @param $sn
     * @return mixed
     */
    public function getFileCacheOfQueue($sn)
    {
        return $this->fileCache->get($sn);
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
     * 从队列中删除订单, 并清除缓存文件
     * @param $sn
     */
    public function removeSnOfQueue($sn)
    {
        $key = self::payQuqueKey($sn);
        $this->queue->remove($key, $sn);
        $this->fileCache->del($sn);
    }

    /**
     * 检查状态是否可更新
     * @param $status
     * @return bool
     */
    public function checkStatusWithStatus($status)
    {
        return !in_array($status,
            [
                DbMessageCenter_PayOrder::STATUS_PAY_SUCCESS,
                DbMessageCenter_PayOrder::STATUS_PAY_CLOSE
            ]);
    }

    /**
     * 从队列中去数据
     * @param null $sn
     * @return mixed
     */
    public function getNotifyQueue($sn = null)
    {
        $key = self::payQuqueKey($sn);
        $data = $this->queue->getMemberByCount($key, 3000);
        return $data;
    }


}