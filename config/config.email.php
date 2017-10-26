<?php
//email QQ
class Config_QQ_Email
{
    //邮箱帐号
    const USERNAME = '';
    //邮箱密码
    const PASSWORD = '';
    //发件人地址地址
    const FROM = '';
    //发件人名字
    const FROM_NAME = '';

    public static function getUserName()
    {
        return self::ACCOUNT_SID;
    }

    public static function getPassWord()
    {
        return self::PASSWORD;
    }

    public static function getFrom()
    {
        return self::FROM;
    }

    public static function getFromName()
    {
        return self::FROM_NAME;
    }

}