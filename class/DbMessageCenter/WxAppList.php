<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/8
 * Time: 13:38
 */
class DbMessageCenter_WxAppList extends Model
{

    const DB_PREFIX = 'message_center.wx_appList';

    public function __construct()
    {
        $this->setTableName(self::DB_PREFIX);
        parent::__construct();
    }



}