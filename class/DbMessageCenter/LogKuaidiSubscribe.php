<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/27
 * Time: 11:09
 */
class DbMessageCenter_LogKuaidiSubscribe extends Model
{
    const DB_PREFIX = 'message_center.log_kuaidiSubscribe';

    public function __construct()
    {
        $this->setTableName(self::DB_PREFIX);
        parent::__construct();
    }


}