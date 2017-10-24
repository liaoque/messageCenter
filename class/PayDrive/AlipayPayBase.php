<?php
require_once ROOT . '/lib/ali/aop/AopClient.php';

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/16
 * Time: 11:33
 */
class PayDrive_AlipayPayBase extends PayDrive_PayBase
{
    const PAY_URL = 'https://openapi.alipay.com/gateway.do';
    const SIGN_TYPE = 'RSA2';
    const INPUT_CHARSET = 'utf-8';
    const PRODUCT_CODE = "QUICK_WAP_PAY";
    const FORMAT = 'json';
    const VERSION = "1.0";

    public static function parseTime($minute)
    {
        return $minute . 'm';
    }

}