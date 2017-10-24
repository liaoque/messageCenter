<?php

if (!isset($_POST["PHPSESSID"])) {
	session_start();
}
//hhh
@define('IN_BOOT', TRUE);
@define('ROOT', dirname(__FILE__));
@define('LOGTIME', 300);//日志产生时间间隔
error_reporting(E_ERROR | E_WARNING | E_PARSE);

date_default_timezone_set("PRC");
$GLOBALS =array();

include ROOT.'/config/config.lang.php';
include ROOT.'/config/config.php';
include ROOT.'/lib/function/common.php';
include ROOT.'/lib/function/array.php';
include ROOT.'/lib/function/date.php';
include ROOT.'/lib/function/http.php';
include ROOT.'/lib/function/parameter.php';
include ROOT.'/lib/function/string.php';
include ROOT.'/lib/smarty/Smarty.class.php';

$magic_quote = get_magic_quotes_gpc();
if(empty($magic_quote)){
	$_GET = saddslashes($_GET);
	$_POST = saddslashes($_POST);
}
/*自动加载类*/
spl_autoload_register('fu_autoload');
spl_autoload_register('class_autoload');

/*实例化redis。 redis是一个key-value存储系统*/
//$redis = PhpRedis::getInstance();
//var_dump($redis);