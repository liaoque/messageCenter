<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/26
 * Time: 21:00
 */
class ProMessageCenter_Message
{
    private $target = '';
    private $appId;
    private $templateId;
//    private $pkId;
    private $content = [];

//    public function setPkId($pkId)
//    {
//        $this->pkId = $pkId;
//        return $this;
//    }

    public function setAppId($appId)
    {
        $this->appId = $appId;
        return $this;
    }

    public function setTemplateId($templateId)
    {
        $this->templateId = $templateId;
        return $this;
    }

    public function setContent($content, $flag = true)
    {
        $this->content = is_string($content) || $flag ? $content : array_merge($this->content, $content);
        return $this;
    }

    public function setTarget($Target)
    {
        $this->target = $Target;
        return $this;
    }

    public function getAppId()
    {
        if (!$this->appId) {
            throw new Exception('appId不存在', -1);
        }
        return $this->appId;
    }

//    public function getPkId()
//    {
//        if ($this->pkId) {
//            throw new Exception('pkId不存在', -1);
//        }
//        return $this->pkId;
//    }

    public function getTemplateId()
    {
        if (empty($this->templateId)) {
            throw new Exception('模版Id不存在', -2);
        }
        return $this->templateId;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getTarget()
    {
        if (empty($this->target)) {
            throw new Exception('发送对象不存在', -3);
        }
        return $this->target;
    }


//    public function getData()
//    {
//        $data = [];
//        foreach ($this as $key => $value) {
//            if (!empty($value)) {
//                $data[$key] = $value;
//            }
//        }
//        return $data;
//    }

    /**
     * @param ProMessageCenter_SmsQueue OR ProMessageCenter_EmailQueue $sender
     * @return mixed
     * @throws Exception
     */
    public function send($sender)
    {
        return $sender->addQueue([
            'appId' => $this->getAppId(),
            'templateId' => $this->getTemplateId(),
            'target' => $this->getTarget(),
            'content' => $this->getContent(),
        ]);
    }

}