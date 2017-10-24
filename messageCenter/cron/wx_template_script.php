<?php
//*/2 * * * * /usr/local/webserver/php/bin/php /home/messageCenter/code/api/cron/Mail_script.php >> /home/messageCenter/code/log/mail.php.log 2>&1
set_time_limit(0);
$logtime = array_sum(explode(' ', microtime()));

@define('TEMPLATE', 'messageCenter');
include dirname(dirname(dirname(__FILE__))) . '/init.php';


function run()
{
    static $templateList = [];
    $template = DbMessageCenter_Template::getInstance();
    $sendDataList = [];
    for ($i = 0; $i < 30; $i++) {
        //获取redis记录
        $proMessageCenterWxTemplateData = ProMessageCenter_WxTemplate::getQueue();
        if (empty($proMessageCenterWxTemplateData)) {
            break;
        }

        $appId = $proMessageCenterWxTemplateData->getAppId();
        $data = [
            'openId' => $proMessageCenterWxTemplateData->getOpenId(),
            'appId' => $appId,
            'templateId' => $proMessageCenterWxTemplateData->getTemplateId(),
            'content' => $proMessageCenterWxTemplateData->getContent(),
            'ext' => Model::enCode($proMessageCenterWxTemplateData->getExt()),
            'ip' => $proMessageCenterWxTemplateData->getIp(),
            'type' => $proMessageCenterWxTemplateData->getType(),
        ];
        try {

            $config = WxDrive_Config::createConfigByAppId($appId);
            $proMessageCenterWxTemplate = new ProMessageCenter_WxTemplate($config);
            if ($data['type'] == DbMessageCenter_LogWxTemplate::TYPE_ONE) {

                $proMessageCenterWxTemplate->one($proMessageCenterWxTemplateData);
            } else {

                $proMessageCenterWxTemplate->all($proMessageCenterWxTemplateData);
            }
            $data['status'] = DbMessageCenter_LogWxTemplate::STATUS_SEND_OK;
        } catch (Exception $e) {
            $data['status'] = DbMessageCenter_LogWxTemplate::STATUS_SEND_ERROR;
            log_message('wxTemplate', 'error:' . $e->getMessage() . 'data:[' . json_encode($data) . ']', 'wxTemplate');
        }
        $sendDataList[] = $data;

    }
    if (!empty($sendDataList)) {
        DbMessageCenter_LogWxTemplate::getInstance()->insert_batch($sendDataList);
    }
}


run();


//结束用时
$logetime = array_sum(explode(' ', microtime()));
$lives = number_format(($logetime - $logtime), 6);
//写日志
$string = __FILE__ . "    :" . date('Y-m-d H:i:s', $logtime) . "  :" . $lives . "secs\r\n";
echo $string;
exit;