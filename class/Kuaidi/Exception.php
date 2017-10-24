<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/6
 * Time: 10:03
 */
class Kuaidi_Exception extends Exception
{
    const STATUS_SUCCESS = 200;
    const STATUS_FORBIDDEN_SIGN = 403;
    const STATUS_FORBIDDEN_PARAM = 40301;

    const STATUS_FORBIDDEN_SERVICE = 500;
    const STATUS_FORBIDDEN_WAYBILL = 404;
    const STATUS_FORBIDDEN_COMPANYNUM = 40401;


    public static $status = [
        self::STATUS_FORBIDDEN_SIGN => '签名错误',
        self::STATUS_SUCCESS => '成功',
        self::STATUS_FORBIDDEN_SERVICE => '服务器错误',
        self::STATUS_FORBIDDEN_PARAM => '参数不正确',
        self::STATUS_FORBIDDEN_WAYBILL => '快递单号不存在',
        self::STATUS_FORBIDDEN_COMPANYNUM => '快递公司不存在'
    ];


    function __construct($code = null, $message = null, Exception $previous = null)
    {
        if (empty($message)) {
            $message = self::$status[$code];
        }
        parent::__construct($message, $code, $previous);
    }


}