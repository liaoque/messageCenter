<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/11
 * Time: 16:47
 */
class ProMessageCenter_WxTemplate extends ProMessageCenter_WxConsoleBase
{
    const QUEUE_KEY = 'mc:p:wx:q';

    public function one(ProMessageCenter_WxTemplateData $proMessageCenterWxTemplateData)
    {
        $openId = $proMessageCenterWxTemplateData->getOpenId();
        $data = Model::deCode($proMessageCenterWxTemplateData->getContent());
        $templateId = $proMessageCenterWxTemplateData->getTemplateId();
        $ext = $proMessageCenterWxTemplateData->getExt();
        if (empty($data['first']) || empty($data['remark'])) {
            throw new Exception('$data: first, remark字段不能为空', -500);
        }
        $dbMessageCenterTemplate = Model::factoryCreate('DbMessageCenter_Template');
        $result = $dbMessageCenterTemplate->findByIdOfCache($templateId);
        if ($result['type'] != DbMessageCenter_Template::TYPE_WX) {
            throw new Exception('模版类型错误,请使用微信模版', -500);
        }
        $templateNum = $result['templateNum'];
        $result = DbMessageCenter_Template::parseTemplate($result['content'], $result['type']);
        $content = array_merge([], array_flip($result), $data);
        $url = $ext['url'];
        $miniprogram = [
            'appid' => $ext['appid'],
            'pagepath' => $ext['pagepath'],
        ];
        $proMessageCenterWxAccessToken = new ProMessageCenter_WxAccessToken($this->getConfig());
        $appId = $this->getConfig()->getLocalAppId();
        $wxDriveServiceTemplateMsg = new WxDrive_ServiceTemplateMsg();
        $wxDriveServiceTemplateMsg->sendText(
            $proMessageCenterWxAccessToken->getAccessToken($appId),
            $openId,
            $templateNum,
            $content,
            $url,
            $miniprogram
        );
        return true;
    }


    public function all(ProMessageCenter_WxTemplateData $proMessageCenterWxTemplateData)
    {
        $data = Model::deCode($proMessageCenterWxTemplateData->getContent());
        $proMessageCenterWxAccessToken = new ProMessageCenter_WxAccessToken($this->getConfig());
        $appId = $this->getConfig()->getLocalAppId();
        $wxDriveServiceTemplateMsg = new WxDrive_ServiceMsgAll();
        $result = $wxDriveServiceTemplateMsg->sendText(
            $proMessageCenterWxAccessToken->getAccessToken($appId),
            $data['content']
        );
        $result = Model::deCode($result);
        if ($result['errcode']) {
            throw new Exception('群发失败:errorCode:' . $result['errcode'] . ' errorMes:' . $result['errmsg'], -500);
        }
        return true;
    }

    public function addQueue(ProMessageCenter_WxTemplateData $data)
    {
        $key = self::getQueueKey();
        return Cache_Queue::getInstance()->set($key, $data->__toString());
    }

    /**
     * @return string
     */
    public static function getQueueKey()
    {
        return self::QUEUE_KEY;
    }

    public static function getQueue()
    {
        $key = self::getQueueKey();
        $result = Cache_Queue::getInstance()->get($key);
        if ($result) {
            $result = new ProMessageCenter_WxTemplateData(Model::deCode($result));
        }
        return $result;
    }


}