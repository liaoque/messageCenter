<?php
if (!defined('IN_BOOT')) {
    exit('Access Denied');
}

class ProMessageCenter_EmailBase extends RouteBase
{
    //选择日至表
    const LOG_BEFORE_TABLE = 1;
    const LOG_AFTER_TABLE = 2;

    //消息发送是否成功
    const SUCCESS = 1;
    const FAILED = 2;

    //用于记录全局Log日志自增ID
    const API_KEY = 'message:center:global';


    public $param = array();
    public $redis = null;
    public $appKey = null;

    public function __construct()
    {
        parent::__construct();
        $this->redis = PhpRedis::getInstance();
    }

    function check_app($appId)
    {
        $_GET['debug'] = 1;
        $app = DbMessageCenter_EmailApp::getInstance();
        $result = $app->find(['id' => $appId], 'keyen');
        if (empty($result)) {
            $this->form->set_message('check_app', '未知的父级应用');
            return false;
        }
        $this->appKey = $result['keyen'];
        return true;
    }

    function check_template($tmpId)
    {
        $mail = DbMessageCenter_EmailAppTemplate::getInstance();
        $result = $mail->find(['id' => $tmpId], 'id');
        if (empty($result)) {
            $this->form->set_message('check_template', '非法的邮件模板');
            return false;
        }
        return true;
    }

    function check_sign($sign)
    {
        $result = self::isSign($this->param, $sign, $this->appKey);
        if (!$result) {
            $this->form->set_message('check_sign', '无效提交');
            return false;
        }
        return true;
    }


    public function logMessage($data = array(), $flag = self::LOG_BEFORE_TABLE)
    {
        if ($flag == self::LOG_BEFORE_TABLE) {
            $log = DbMessageCenter_EmailLogSendBefore::getInstance();
        } elseif ($flag == self::LOG_AFTER_TABLE) {
            $log = DbMessageCenter_EmailLogSendAfter::getInstance();
        }
        if ($log) {
            return $log->insert($data);
        }
        return false;
    }
}