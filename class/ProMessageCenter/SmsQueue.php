<?php
if (!defined('IN_BOOT')) {
    exit('Access Denied');
}

class ProMessageCenter_SmsQueue
{
    const QUEUE = 'mc:sms:queue';
    const QUEUE_PKID = 'mc:sms:pkId';
    const TIMEOUT = 600;


    public function __construct()
    {
        $this->QueueCache = Cache_Queue::getInstance();
    }


    /*
     * 获取队列中邮件消息
     * */
    public function getQueue()
    {
        return $this->QueueCache->get(self::QUEUE);
    }


    public static function getPkIdKey()
    {
        return self::QUEUE_PKID;
    }

    public static function getPkId()
    {
        $redis = PhpRedis::getInstance();
        return $redis->incr(self::QUEUE_PKID);
    }

    public static function getQueueName()
    {
        return self::QUEUE;
    }

    /** 将消要发送息存入队列
     * @param array $data
     * @return bool
     */
    public function addQueue($data = array())
    {
        $pkId = self::getPkId();
        if ($pkId) {
            $data['pkId'] = $pkId;
            if ($this->QueueCache->set(self::getQueueName(), $data)) {
                return $pkId;
            }
        }
        return false;
    }
}