<?php
/************************************************
 * php test.php [-f testConfig.php]
 * -f 指定文件, 如不指定 默认指定 testConfig.php
 * -p 遍历test文件下所有目录, 测试所有类的文件中的方法
 ************************************************/
header("Content-type:text/html;charset=utf-8");

@define('TEMPLATE', 'messageCenter');
@define('PRO_ROOT', dirname(__FILE__));
include_once dirname(dirname(__FILE__)) . '/init.php';
include_once 'lib/func.php';

$httpRequest = Model::factoryCreate('ProMessageCenter_Request');
$httpRequest->setCurl(Model::factoryCreate('Test_RequestBase'));

$fileNameList = [];
$opt = getopt('f:ph:c:');
if (isset($opt['p'])) {
    /**
     * 遍历所有目录
     */
    $fileNameList = readDirs(PRO_ROOT . '/test');

} else if ($opt['f']) {
    /**
     * 指定文件
     */
    $fileName = $opt['f'];
    $fileNameList = require_once "$fileName";
    foreach ($fileNameList as $key => $v) {
        $fileNameList[$key] = PRO_ROOT . '/test/' . $v . '.php';
    }

} else {
    /***
     * 默认文件
     */
    $fileNameList = require_once 'testConfig/testConfig.php';
    foreach ($fileNameList as $key => $v) {
        $fileNameList[$key] = PRO_ROOT . '/test/' . $v . '.php';
    }

}

if (empty($fileNameList)) {
    Test_ResponesBase::echoMessage(
        '/************************************************
 * php test.php [-f testConfig.php]
 * -f 指定文件, 如不指定 默认指定 testConfig.php
 * -p 遍历test文件下所有目录, 测试所有类的文件中的方法
 ************************************************/');
}

arrayMapAllWidthNokey(function ($fileNameList) {
    if (preg_match('/(\w+)\/(\w+).php$/', $fileNameList, $classNames)) {
        $className = ucfirst($classNames[1]) . '_' . $classNames[2];
        include_once $fileNameList;
        if (!class_exists($className)) {
            Test_ResponesBase::echoMessage($className . '类不存在', false);
        }
        $className = new ReflectionClass($className);
        $methods = $className->getMethods();
        foreach ($methods as $method) {
            call_user_func(array($method->class, $method->name));
        }
    }
}, $fileNameList);






//include_once('test/mail/TestIndex.php');


//$query_string = remove_invisible_characters($_SERVER['QUERY_STRING']);
//$segments = _explode_segments($query_string);
//
//$lastPage = $_SERVER["HTTP_REFERER"];
//$GLOBALS['segments'] = $segments;
//$file_path = "mod/" . $segments['mod'] . "/" . $segments['act'] . ".php";
//$lastPage = $_SERVER["HTTP_REFERER"];


//header("Content-type:text/html;charset=utf-8");
//
//if (file_exists($file_path)) {
//    include_once($file_path);
//    $act = $segments['act'];
//    $ac = $segments['ac'] . 'Func';
//    $obj = new $act($segments);
//    $obj->actionRun();
//} else {
//    show_error('THE URL NOT FOUND', 404);
//}
//The end.






