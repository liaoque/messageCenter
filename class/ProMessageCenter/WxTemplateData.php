<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/11
 * Time: 16:47
 */
class ProMessageCenter_WxTemplateData
{
    private $appId;
    private $wxTemplateId;
    private $openId = '';
    private $templateId = 0;
    private $content;
    private $ext = [];
    private $ip;
    private $type;

    public function __construct($info)
    {
        $this->setDatas($info);
    }

    /**
     * @return mixed
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * @param mixed $appId
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;
    }

    /**
     * @return mixed
     */
    public function getOpenId()
    {
        return $this->openId;
    }

    /**
     * @param mixed $openId
     */
    public function setOpenId($openId)
    {
        $this->openId = $openId;
    }

    /**
     * @return mixed
     */
    public function getTemplateId()
    {
        return $this->templateId;
    }

    /**
     * @param mixed $templateId
     */
    public function setTemplateId($templateId)
    {
        $this->templateId = $templateId;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getExt()
    {
        return [
            'url' => empty($this->ext['url']) ? '' : $this->ext['url'],
            'appid' => empty($this->ext['appid']) ? '' : $this->ext['appid'],
            'pagepath' => empty($this->ext['pagepath']) ? '' : $this->ext['pagepath'],
        ];
    }

    /**
     * @param mixed $ext
     */
    public function setExt($ext)
    {
        $this->ext = $ext;
    }

    /**
     * @return mixed
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param mixed $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getWxTemplateId()
    {
        return $this->wxTemplateId;
    }

    /**
     * @param mixed $wxTemplateId
     */
    public function setWxTemplateId($wxTemplateId)
    {
        $this->wxTemplateId = $wxTemplateId;
    }


    public function send()
    {
        $appId = $this->getAppId();
        $config = WxDrive_Config::createConfigByAppId($appId);
        $proMessageCenterWxTemplate = new ProMessageCenter_WxTemplate($config);
        return $proMessageCenterWxTemplate->addQueue($this);
    }


    public function validation()
    {
        $templateId = $this->getTemplateId();
        return DbMessageCenter_Template::validation($templateId, DbMessageCenter_Template::TYPE_WX);
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
        $data = [];
        foreach ($this as $key => $value) {
            if (!empty($value)) {
                $data[$key] = $value;
            }
        }
        return Model::enCode($data, 1);
    }

    public function setDatas($datas)
    {
        foreach ($datas as $key => $value) {
            $action = 'set' . ucfirst($key);
            if (method_exists($this, $action)) {
                $this->$action($value);
            }
        }
    }


}