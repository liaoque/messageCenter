<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/13
 * Time: 15:21
 */
class ProMessageCenter_Push
{
    const QUEUE_KEY = 'mc:p:app:q';

    private $queue;

    public function __construct()
    {
        $this->queue = Cache_Queue::getInstance();
    }


    /**
     * @param ProMessageCenter_PushData $jPushData
     * @param $key
     * @param $secret
     * @return jPushDrive_Client
     * @throws Exception
     */
    public function createClient(ProMessageCenter_PushData $jPushData, $key, $secret)
    {
        switch ($jPushData->getTarget()) {
            case ProMessageCenter_PushData::TARGET_ALL:
                $action = 'setAudience';
                break;
            case 0:
                throw new Exception(-500, '请设置发送对象');
                break;
            default:
                $action = 'addAlias';
        }
        $push = new jPushDrive_Client($key, $secret);
        $extras = Model::deCode($jPushData->getExtras());
        $push = $push->push()
            ->setPlatform($jPushData->getPlatform())
            ->$action($jPushData->getTarget())
            ->setNotificationAlert($jPushData->getAlter())
            ->androidNotification($jPushData->getAlter(), [
                'title' => $jPushData->getTitle(),
                'builder_id' => $jPushData->getAndroidBuilderId(),
                'priority' => $jPushData->getAndroidPriority(),
                'style' => $jPushData->getAndroidStyle(),
                'big_text' => $jPushData->getAndroidBigText(),
                'inbox' => $jPushData->getAndroidInbox(),
                'big_pic_path' => $jPushData->getAndroidBigPicPath(),
                'category' => $jPushData->getAndroidCategory(),
                'extras' => $extras
            ])
            ->iosNotification($jPushData->getAlter(), [
                'sound' => $jPushData->getIosSound(),
                'badge' => $jPushData->getIosBadge(),
                'content-available' => $jPushData->getIosMutableContent(),
                'mutable-content' => $jPushData->getIosMutableContent(),
                'category' => $jPushData->getIosCategory(),
                'extras' => $extras
            ])
            ->addWinPhoneNotification(
                $jPushData->getAlter(),
                $jPushData->getTitle(),
                $jPushData->getWebPhoneOpenPage(),
                $extras
            )
            ->options([
                'sendno' => $jPushData->getSendno(),
                'time_to_live' => $jPushData->getTimeToLive(),
                'override_msg_id' => $jPushData->getOverrideMsgId(),
                'apns_production' => $jPushData->getApnsProduction(),
                'big_push_duration' => $jPushData->getBigPushDuration()
            ]);
        return $push;
    }

    /**
     * @param jPushDrive_Client $push
     * @throws Exception
     */
    public function send($push)
    {

        if (!is_object($push) || !method_exists($push, 'send')) {
            throw new Exception(-500, '$push 必须是一个对象, 且必须有send方法');
        }
        $push->send();
//        $this->push->__destruct();
//        $this->push = null;
    }

    /**
     * @param ProMessageCenter_PushData $jPushData
     * @return mixed
     */
    public function addQueue(ProMessageCenter_PushData $jPushData)
    {
        $str = $jPushData . '';
        $key = self::getQueueKey();
        return $this->queue->set($key, $str);
    }

    /**
     * @return string
     */
    public static function getQueueKey()
    {
        return self::QUEUE_KEY;
    }

    /**
     * @return ProMessageCenter_PushData
     */
    public function getQueue()
    {
        $key = self::getQueueKey();
        $result = $this->queue->get($key);
        if ($result) {
            $result = new ProMessageCenter_PushData(Model::deCode($result));
        }
        return $result;
    }


}