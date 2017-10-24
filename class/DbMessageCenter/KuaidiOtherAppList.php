<?php
if (!defined('IN_BOOT')) {
    exit('Access Denied');
}

class DbMessageCenter_KuaidiOtherAppList extends Model
{
    const DB_PREFIX = 'message_center.kuaidi_otherAppList';
    const REDIS_KEY = 'mc:kdoal:';

    const STATUS_OPEN = 1;
    const STATUS_CLOSE = 2;

    public static $status = [
        self::STATUS_OPEN => '开启',
        self::STATUS_CLOSE => '关闭',
    ];


    public function __construct()
    {
        $this->setTableName(self::DB_PREFIX);
        parent::__construct();
    }


}