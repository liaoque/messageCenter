<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/31
 * Time: 10:31
 */
class Kuaidi_Service implements Kuaidi_BaseService
{

    public $data;

    public $config = [];

    private $drivice = null;

    public function __construct($otherAppId = 0)
    {
        if (empty($this->getDrivice())) {
            $this->init($otherAppId);
            $className = 'Kuaidi_' . $this->config['className'];
            if (!class_exists($className)) {
                throw new Exception('快递对象不存在');
            }
            $this->drivice = new $className($this->config);
        }
    }

    public function init($otherAppId = 0)
    {
        /**
         * 目前使用了配置文件,
         * 如果接口多的话,
         * 完全可以改写这个方法,
         * 使用数据库接口信息
         */
        if ($otherAppId) {
            $app = DbMessageCenter_KuaidiOtherAppList::getInstance()->find(['id' => $otherAppId]);
        } else {
            $app = DbMessageCenter_KuaidiOtherAppList::getInstance()->findRandAppOfCache();
        }
        if (!empty($app)) {
            $this->config = [
                'appId' => $app['id'],
                'className' => DbMessageCenter_KuaidiOtherAppList::getClassName($app['type']),
                'notifyUrl' => $app['notifyUrl'],
                'appKey' => $app['appKey'],
                'salt' => $app['salt'],
            ];
        }
    }

    protected function getDrivice()
    {
        return $this->drivice;
    }


    /**
     * 订阅
     * @param Kuaidi_Data $kuaiDiInfo
     * @return mixed
     */
    public function subscribe(Kuaidi_Data $kuaiDiInfo)
    {
        $_data = [
            //快递公司
            'companyName' => $kuaiDiInfo->getOtherCompanyName($this->config),

            //快递公司编码
            'companyNum' => $kuaiDiInfo->getOtherCompanyNum($this->config),

            //快递单号
            'waybill' => $kuaiDiInfo->getWaybill(),

            //出发城市
            'from' => $kuaiDiInfo->getFrom(),

            //目的地
            'to' => $kuaiDiInfo->getTo(),

        ];
        return $this->getDrivice()->subscribe($_data);
    }

    /**
     * @param Kuaidi_Data $data
     * @param $appInfo 回调的app应用
     * @param array $ext 扩展对象
     * @return mixed
     */
    public function publish(Kuaidi_Data $data, $appInfo, $ext = array())
    {
        $_data = [
            'companyNum' => $data->getCompanyNum(),
            'num' => $data->getWaybill(),
            'to' => $data->getTo(),
            'from' => $data->getFrom(),
            'time' => $data->getTime(),
            'context' => $data->getContext(),
            'state' => $data->getState()
        ];
        $_data['sign'] = RouteBase::createSign($_data, $appInfo['key']);
        $result = Http_RequestBase::curlGet($appInfo['notifyUrl'], $_data);
        return $result;
    }

    /**
     * 查询
     * @param Kuaidi_Data $data
     * @return mixed
     */
    public function getOfCurl(Kuaidi_Data $data)
    {
        $_data = $data;
        return $this->getDrivice()->getOfCurl($_data);
    }

    /**
     * 回调
     * @param $data
     * @return mixed
     *          [
     *              // 公司编号
     *              'companyNum',
     *              // 快递编码
     *              'waybill' ,
     *              //具体状态
     *              'status',
     *              // 数据
     *              'data' ,
     *              // 是否需要重新订阅
     *              'restart',
     *              // message
     *              'message'
     *          ]
     */
    public function notify($data)
    {
        return $this->getDrivice()->notify($data);
    }

    /**
     * @param $code 状态吗,  1. 是正确, 2.是错误
     * @param array|string $mes 扩展信息,具体类型根据 快递的具体接口来定
     * @return mixed
     */
    public function response($code, $mes)
    {
        return $this->drivice->response($code, $mes);
    }


    public function checkRestart($data)
    {
        return $this->drivice->checkRestart($data);
    }

    public function checkNotifySign($result)
    {
        log_message('EXPRESS INFO', json_encode($this->config), 'salt');
        return $this->drivice->checkNotifySign($result, $this->config);
    }

    public function getConfig($name = null)
    {
        if ($name) {
            $result = empty($this->config[$name]) ? '' : $this->config[$name];
        } else {
            $result = $this->config;
        }
        return $result;
    }
}