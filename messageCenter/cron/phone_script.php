<?php
//*/2 * * * * /usr/local/webserver/php/bin/php /home/messageCenter/code/api/cron/Mail_script.php >> /home/messageCenter/code/log/mail.php.log 2>&1

set_time_limit(0);
$logtime = array_sum(explode(' ', microtime()));

@define('TEMPLATE', 'passport');
include dirname(dirname(dirname(__FILE__))) . '/init.php';
include dirname(dirname(dirname(__FILE__))) . '/lib/function/sms.php';

function insertData()
{
    static $templateList = [];
    $model_mail = Model::factoryCreate('ProMessageCenter_SmsQueue');
    $template = DbMessageCenter_Template::getInstance();
    $sendDataList = [];
    for ($i = 0; $i < 30; $i++) {
        //获取redis记录
        $data = $model_mail->getQueue();
        if (empty($data)) {
            break;
        }

        //TODO::发送邮件
        $data = json_decode($data, true);
        $templateId = $data['templateId'];
        if (empty($templateList[$templateId])) {
            $templateList[$templateId] = $template->findByIdOfCache($templateId);
        }
        if (!empty($templateList[$templateId])) {
            $templateData = $templateList[$templateId];

            $content = DbMessageCenter_Template::parseTemplate($templateData['content'], DbMessageCenter_Template::TYPE_PHONE);
            $content = array_intersect_key(array_merge($content, $data['content']), $content);
            $data['status'] = DbMessageCenter_LogPhone::STATUS_SEND_ERROR;
            if (!empty($content)) {
                $_data = [];
                foreach ($content as $v) {
                    $_data[] = $data['content'][$v];
                }
                $content = $_data;
                //由于 log_mail_content表中使用logId 对应 log_mail的主键, 所以只能一个个查
                $data['status'] = SEND_PHONE_MES && sendTemplateSMS($data['target'], $content, $templateData['templateNum']) ? DbMessageCenter_LogPhone::STATUS_SEND_OK : DbMessageCenter_LogPhone::STATUS_SEND_ERROR;
            }
            $data['content'] = Model::enCode($data['content'], true);
            DbMessageCenter_LogPhone::getInstance()->insert($data);
        }
    }

    return $sendDataList;
}


insertData();


//结束用时
$logetime = array_sum(explode(' ', microtime()));
$lives = number_format(($logetime - $logtime), 6);
//写日志
$string = __FILE__ . "    :" . date('Y-m-d H:i:s', $logtime) . "  :" . $lives . "secs\r\n";
echo $string;
exit;