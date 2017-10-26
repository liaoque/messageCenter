<?php

//云通信
class Config_Sms
{
    //填写快递的相关参数
    const ACCOUNT_SID = '';
    const ACCOUNT_TOKEN = '';
    const APPID = '';

    public static function getAccountSid()
    {
        return self::ACCOUNT_SID;
    }

    public static function getAccountToken()
    {
        return self::ACCOUNT_TOKEN;
    }

    public static function getAppId()
    {
        return self::APPID;
    }

}
