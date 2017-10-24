<?php
if (!defined('IN_BOOT')) {
    exit('Access Denied');
}

class DbMessageCenter_LogPush extends Model
{
    const DB_PREFIX = 'message_center.log_push';

    const TYPE_ONE = 1;
    const TYPE_ALL = 2;

    const STATUS_SEND_OK = 1;
    const STATUS_SEND_ERROR = 2;
    const STATUS_SEND_ING = 3;

    public static $status = [
        self::STATUS_SEND_OK => '成功',
        self::STATUS_SEND_ERROR => '失败',
        self::STATUS_SEND_ING => '进行中'
    ];

    public function __construct()
    {
        $this->setTableName(self::DB_PREFIX);
        parent::__construct();
    }
}