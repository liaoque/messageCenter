<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/9
 * Time: 17:23
 */
class DbMessageCenter_PayRefound extends Model
{
    const DB_PREFIX = 'message_center.pay_refound';
    const REDIS_KEY = 'mc:prd:';

    const REFOUND_STATUS_SUCESS = 1;
    const REFOUND_STATUS_ERROR = 2;
    const REFOUND_STATUS_ING = 3;
    const REFOUND_STATUS_CLOSE = 4;

    public static $status = [
        self::REFOUND_STATUS_SUCESS => '退款成功',
        self::REFOUND_STATUS_ERROR => '退款失败',
        self::REFOUND_STATUS_ING => '退款中',
        self::REFOUND_STATUS_CLOSE => '退款关闭'
    ];

    public function __construct()
    {
        $this->setTableName(self::DB_PREFIX);
        parent::__construct();
    }





}