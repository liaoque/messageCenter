<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/31
 * Time: 11:09
 */
class Config_KuaiDi
{
	//填写快递的相关参数
    const appId = 1;
    const className = 'KuaiDi100';
    const notifyUrl = '';
    const appKey = '';
    const salt = '1234536';


    public static function getAppId()
    {
        return self::appId;
    }

    public static function getClassName()
    {
        return self::className;
    }

    public static function getNotifyUrl()
    {
        return self::notifyUrl;
    }

    public static function getAppKey()
    {
        return self::appKey;
    }

    public static function getSalt()
    {
        return self::salt;
    }
}

