<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/31
 * Time: 16:43
 */
class ProMessageCenter_KuaiDiSubscribe
{


    public static function getQueueKey()
    {
        return 'mc:kds:queue';
    }


    public function addQueue($data)
    {
        $key = self::getQueueKey();
        return Cache_Queue::getInstance()->set($key, $data);
    }

    public function getQueue()
    {
        $key = self::getQueueKey();
        return Cache_Queue::getInstance()->get($key);
    }

//    public function removeQueue($data)
//    {
//        $key = self::getQueueKey();
//        return Cache_Queue::getInstance()->del($key, $data);
//    }

    /**
     * @param $info
     * [
     *      appId       //应用ID
     *      companyNum  //快递公司编号
     *      waybill     //快递单号
     *      origin      //出发城市
     *      target      //目标城市
     *      ip          //ip
     * ]
     * @return mixed
     */
    public function subcribe($info)
    {
        $result = DbMessageCenter_LogKuaidiSubscribe::getInstance()->insert([
            'appId' => $info['appId'],
            'companyListId' => $info['companyListId'],
            'waybill' => $info['waybill'],
            'ip' => $info['ip']
        ]);
        if ($result) {
            unset($info['ip']);
            unset($info['appId']);
            $result = $this->addQueue($info);
        }
        return $result;
    }


    /**
     * @param $id
     * @param $status
     * @param $data
     * @param bool $restart 重新订阅
     * @return mixed
     */
    public function updateStatusById($id, $data, $status, $restart = false)
    {
        $info = [
            'id' => $id,
//            'data' => $data,
            'status' => $status,
            'restart' => $restart
        ];

        $result = $this->addQueue($info);
        return $result;
    }


}