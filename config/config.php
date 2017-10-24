<?php
if (!defined('IN_BOOT')) {
    exit('Access Denied');
}
$_SC = array();
$v = require_once 'config.include.php';
include(ROOT . '/config/config.' . $v . '.php');

$_SC['imgpath_replace'] = '{{img}}';
$_SC['dbcharset'] = 'utf8'; //字符集
$_SC['pconnect'] = 0; //是否持续连接
/**
 * cookie
 */// COOKIE前缀
$_SC['cookiepre'] = array(
    'xxx' =>'ad_'
);
$_SC['cookiedomain'] = '.xxx.com'; //COOKIE作用域
$_SC['cookiepath'] = '/'; //COOKIE作用路径
$_SC['cookietime'] = 86400; //COOKIE时间
$_SC['mobile'] = array('time' => 300, 'info' => '5', 'tempId' => '129486', 'second' => 60); //手机验证码发送规则
$_SC['mobile_price_notice'] = array('time' => 300, 'info' => '5', 'tempId' => '142726', 'second' => 60); //手机验证码发送规则
$_SC['mobile_advance_notice'] = array('time' => 300, 'info' => '5', 'tempId' => '149734', 'second' => 60); //手机验证码发送规则
$_SC['email'] = array('time' => 1800, 'info' => '30', 'second' => '60'); //手机验证码发送规则
// 是否开启缓存
$_SC['cache'] = false;

@define("QQ", '/^[1-9][0-9]{4,}$/');
// 用于cookie加密
@define('WEBKEY', '2132132132131');




?>