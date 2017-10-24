<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/4
 * Time: 15:38
 */
class Test_ResponesBase
{

    public function __construct()
    {
//        assert_options(ASSERT_ACTIVE, 1);
//        assert_options(ASSERT_WARNING, 0);
//        assert_options(ASSERT_BAIL, 1);
//        assert_options(ASSERT_CALLBACK, [$this, "assert_show_error"]);
    }

    public static function showMessage($flag, $mes = null)
    {

        try {
            if(!$flag){
                throw new Exception($mes);
            }
            self::echoMessage($mes, 'green');
        } catch (Exception $e) {
            self::echoMessage($e->getMessage(), 'red');
            array_map(function($trace){
                if($trace['file']){
                    self::echoMessage("\t\t 报错文件:".$trace['file']."\t 报错行号:".$trace['line'], 'red');
                }
            }, $e->getTrace());
        }
    }


//    public function assert_show_error($file, $line, $code, $desc = null)
//    {
//
//        self::echoMessage($desc, false);
//    }


    public static function echoMessage($mes, $color = 'blue')
    {
        $mes = @iconv("utf-8", "gbk", is_string($mes) ? $mes : Model::enCode($mes, true));
        switch($color){
            case 'blue':
                $flag = '提示!  ';
                $flag = iconv("utf-8", "gbk", $flag);
                echo "\033[34m  $flag  \033[0m  $mes  \n";
                break;
            case 'red':
                $flag = '错误!  ';
                $flag = iconv("utf-8", "gbk", $flag);
                echo "\033[31m  $flag  \033[0m  $mes  \n";
                break;
            case 'green':
                $flag = '正确!  ';
                $flag = iconv("utf-8", "gbk", $flag);
                echo "\033[32m  $flag  \033[0m  $mes  \n";
                break;
        }
    }
}