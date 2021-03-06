<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/31
 * Time: 16:43
 */
class ProMessageCenter_KuaiDiNotify
{


    private $service = null;

    public function addQueue($data)
    {
        $key = ProMessageCenter_KuaiDiSubscribe::getQueueKey();
        return Cache_Queue::getInstance()->set($key, $data);
    }

    /**
     * @param int $otherAppId 快递平台帐号配置id
     * @return Kuaidi_Service|null
     */
    public function getService($otherAppId = 1)
    {
        if (!$this->service) {
            $this->service = new Kuaidi_Service($otherAppId);
        }
        return $this->service;
    }

    /**
     * @param $data
     * @param int $otherAppId 快递平台帐号配置id
     * @return mixed
     * @throws Kuaidi_Exception
     */
    public function notify($data, $otherAppId = 1)
    {
        //校验签名
        parse_str($data, $params);
        if (!$this->checkSign($params, $otherAppId)) {
            throw new Kuaidi_Exception(Kuaidi_Exception::STATUS_FORBIDDEN_SIGN);
        }
        $service = $this->getService($otherAppId);
        return $service->notify($params);
    }

    /**
     * @param $code 状态吗,  1. 是正确, 2.是错误
     * @param array|string $mes 扩展信息,具体类型根据 快递的具体接口来定
     * @param int $otherAppId 快递平台帐号配置id
     * @return mixed
     */
    public function response($code, $mes, $otherAppId = 1)
    {
        $service = $this->getService($otherAppId);
        return $service->response($code, $mes);
    }

    /**
     * @param $result
     * @param int $otherAppId 快递平台帐号配置id
     * @return mixed
     */
    public function checkSign($result, $otherAppId = 1)
    {
        return $this->getService($otherAppId)->checkNotifySign($result);
    }

    /**
     * 获取缓存文件路径
     * @param $id
     * @return string
     */
    public static function getCacheWaybillDataDir($id)
    {
        return ROOT . '/' . TEMPLATE . '/var/waybill/' . $id;
    }

    /**
     * 缓存快递数据
     * @param $id
     * @param $data
     * @return bool|int
     */
    public static function cacheWaybillData($id, $data)
    {
        $fileName = self::getCacheWaybillDataDir($id);
        $result = mkdirs(dirname($fileName));
        if ($result) {
            $result = file_put_contents($fileName, Model::enCode($data, 1));
        }
        return $result;
    }

    /**
     * 获取缓存快递数据
     * @param $id
     * @return array|mixed|string
     */
    public static function getCacheWaybillData($id)
    {
        $fileName = self::getCacheWaybillDataDir($id);
        $result = array();
        if (file_exists($fileName)) {
            $result = file_get_contents($fileName);
        }
        return $result;
    }

    /**
     * 删除缓存快递数据
     * @param $id
     * @return bool
     */
    public static function removeCacheWaybillData($id)
    {
        $fileName = self::getCacheWaybillDataDir($id);
        if (file_exists($fileName)) {
            @unlink($fileName);
        }
        return true;
    }

    /**
     * @param $id
     * @param $data
     * @param $status
     * @param $restart 重新订阅
     * @return mixed
     */
    public function updateStatusById($id, $data, $status, $restart = fals)
    {
        log_message('EXPRESS INFO', Model::enCode($data, 1),'json');
        if (!self::cacheWaybillData($id, $data)) {
            return false;
        }
        $subscribe = Model::factoryCreate('ProMessageCenter_KuaiDiSubscribe');
        $info = [
            'id' => $id,
//            'data' => $data,
            'status' => $status,
            'restart' => $restart
        ];
        return $result = $subscribe->addQueue($info);
    }

}