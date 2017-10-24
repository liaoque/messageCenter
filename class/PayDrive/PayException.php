<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/9
 * Time: 18:10
 */
class PayDrive_PayException extends Exception
{
    //请求支付失败
    const ERROR_REQUEST_PAY = -101;
    const ERROR_REQUEST_FILECACHE_SAVE = -102;
    //属性为空
    const ERROR_PRO_EMPTY = -102;

    //系统错误, 如数据库插入失败什么的
    const ERROR_SYS = -500;
    const ERROR_AUTH = -401;

    //三方错误
    const ERROR_OTHER = -1000;
    const ERROR_OTHER_SIGN = -1001;
    const ERROR_OTHER_MONEY = -1002;

    //应用级错误
    const ERROR_APP = 2000;


    public static $mes = [
        self::ERROR_REQUEST_PAY => '请求创建订单号失败.',
        self::ERROR_REQUEST_FILECACHE_SAVE => '文件缓存保存失败.',
        self::ERROR_OTHER => '支付失败.',
        self::ERROR_OTHER_SIGN => '签名错误.',
        self::ERROR_OTHER_MONEY => '支付金额小于0.',
    ];


}