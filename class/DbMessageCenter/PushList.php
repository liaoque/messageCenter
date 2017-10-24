<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/8
 * Time: 13:38
 */
class DbMessageCenter_PushList extends Model
{

    const DB_PREFIX = 'message_center.push_list';

    const STATUS_OPEN = 1;

    public function __construct()
    {
        $this->setTableName(self::DB_PREFIX);
        parent::__construct();
    }



}