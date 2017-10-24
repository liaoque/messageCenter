<?php
//*/2 * * * * /usr/local/webserver/php/bin/php /home/messageCenter/code/api/cron/Mail_script.php >> /home/messageCenter/code/log/mail.php.log 2>&1

set_time_limit(0);
$logtime = array_sum(explode(' ', microtime()));

@define('TEMPLATE', 'messageCenter');
include dirname(dirname(dirname(__FILE__))) . '/init.php';


/**
 * 查找需要发送请求的数据
 *
 */
DbMessageCenter_KuaidiWaybill::getInstance()
    //处理订阅数据
    ->subscribe()
    //保存队列中的数据
    ->saveQueueData();



//结束用时
$logetime = array_sum(explode(' ', microtime()));
$lives = number_format(($logetime - $logtime), 6);
//写日志
$string = __FILE__ . "    :" . date('Y-m-d H:i:s', $logtime) . "  :" . $lives . "secs\r\n";
echo $string;
exit;