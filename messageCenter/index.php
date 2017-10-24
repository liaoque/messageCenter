<?php
@define('TEMPLATE', 'messageCenter');
@define('WEBROOT', dirname(__FILE__));
include_once dirname(dirname(__FILE__)) . '/init.php';
include_once 'lib/func.php';

$query_string = remove_invisible_characters($_SERVER['QUERY_STRING']);
$segments = _explode_segments($query_string);

$lastPage = $_SERVER["HTTP_REFERER"];
$GLOBALS['segments'] = $segments;
$file_path = dirname(__FILE__)."/mod/" . $segments['mod'] . "/" . $segments['act'] . ".php";
$lastPage = $_SERVER["HTTP_REFERER"];

header("Content-type:text/html;charset=utf-8");

if (file_exists($file_path)) {
    include_once($file_path);
    $ac = $segments['ac'] . 'Func';
    $className = ucfirst($segments['mod']) . '_' . ucfirst($segments['act']);
    $obj = new $className();
    $obj->init($segments);
    $obj->actionRun();
} else {
    show_message('THE URL NOT FOUND', '/', 2);
}
//The end.






