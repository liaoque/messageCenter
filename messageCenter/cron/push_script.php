<?php
//*/2 * * * * /usr/local/webserver/php/bin/php /home/messageCenter/code/api/cron/Mail_script.php >> /home/messageCenter/code/log/mail.php.log 2>&1
set_time_limit(0);
$logtime = array_sum(explode(' ', microtime()));

@define('TEMPLATE', 'messageCenter');
include dirname(dirname(dirname(__FILE__))) . '/init.php';


function run()
{
    static $templateList = [];
    $pushList = Model::factoryCreate('DbMessageCenter_PushList')->find([
        'status' => DbMessageCenter_PushList::STATUS_OPEN
    ], 'appKey, appSecret');
    if (empty($pushList)) {
        return;
    }
    $template = DbMessageCenter_Template::getInstance();
    $proMessageCenterPush = new ProMessageCenter_Push;
    $sendDataList = [];
    for ($i = 0; $i < 30; $i++) {
        //获取redis记录
        $proMessageCenterjPushData = $proMessageCenterPush->getQueue();
        if (empty($proMessageCenterjPushData)) {
            break;
        }
        //TODO::发送邮件
        $templateId = $proMessageCenterjPushData->getTemplateId();
        if (empty($templateList[$templateId])) {
            $result = $template->findByIdOfCache($templateId);
            if (empty($result)) {
                $templateList[$templateId] = -1;
            } else {
                $templateList[$templateId] = DbMessageCenter_Template::parseTemplate(
                    $result['content'],
                    DbMessageCenter_Template::TYPE_APP
                );
            }
        }

        if ($templateList[$templateId] != -1) {
            $content = $templateList[$templateId];
            $proMessageCenterjPushData->setDatas($content);
            $appId = $proMessageCenterjPushData->getAppId();
            $data = [
                'target' => $proMessageCenterjPushData->getTarget(),
                'appId' => $appId,
                'templateId' => $proMessageCenterjPushData->getTemplateId(),
                'content' => $proMessageCenterjPushData->getExtras(),
                'ip' => $proMessageCenterjPushData->getIp(),
            ];
            try {
//                $config = WxDrive_Config::createConfigByAppId($appId);
                $proMessageCenterWxTemplate = new ProMessageCenter_Push();
                $proMessageCenterWxTemplate->send($proMessageCenterWxTemplate->createClient($proMessageCenterjPushData, $pushList['appKey'], $pushList['appSecret']));
                $data['status'] = DbMessageCenter_LogWxTemplate::STATUS_SEND_OK;
            } catch (Exception $e) {
                $data['status'] = DbMessageCenter_LogWxTemplate::STATUS_SEND_ERROR;
                log_message('push', 'error:' . $e->getMessage() . 'data:[' . $proMessageCenterjPushData . ']', 'push');
            }
        }
        $sendDataList[] = $data;
    }
    if (!empty($sendDataList)) {
        DbMessageCenter_LogPush::getInstance()->insert_batch($sendDataList);
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