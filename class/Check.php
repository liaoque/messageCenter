<?php
class Check {

    public function __construct() {

    }

    static private $instance = NULL;
    static function getInstance() {
        if(self::$instance == NULL) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    function checkInput($str) {
        if (empty($str)) {
            return true;
        }
        if (preg_match('/[\xf0-\xf7].{3}/', $str)) {

            show_message('不允许输入表情符号','',2,'');

            return false;
        }

        return true;
    }



}