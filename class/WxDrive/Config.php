<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/26
 * Time: 9:24
 */
class WxDrive_Config
{


    private $appId;
    private $localAppId;
    private $appSecret;
    private $token;
    private $encodingAESKey;
    private $encodingOldAESKey;
    private $payKey;
    private $mchId;

    public static function createConfigByAppId($appId)
    {
        $dbMessageCenterWxAppList = Model::factoryCreate('DbMessageCenter_WxAppList');
        $result = $dbMessageCenterWxAppList->find([
            'appId' => $appId
        ], 'wxAppId as appId, appSecret, token, encodingAESKey, encodingOldAESKey');
        if (empty($result)) {
            throw new Exception(-500, '微信公众号配置并未找到; appId:' . $appId);
        }
        $result['localAppId'] = $appId;
        return new self($result);
    }

    /**
     * WxDrive_Config constructor.
     * @param array $config
     *                  $appId
     *                  $appSecret
     *                  $token
     *                  $encodingOldAESKey
     *                  $encodingAESKey
     *                  $payKey
     */
    public function __construct($config = array())
    {
//        $appId, $appSecret, $token, $encodingOldAESKey, $encodingAESKey, $payKey
        foreach ($config as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function getAppId()
    {
        return $this->appId;
    }

    public function getAppSecret()
    {
        return $this->appSecret;
    }

    public function getToken()
    {
        return $this->token;
    }

    // 公众平台上，开发者设置的EncodingAESKey
    public function getEncodingAesKey()
    {
        return $this->encodingAESKey;
    }

    public function getEncodingOldAesKey()
    {
        return $this->encodingOldAESKey;
    }


    /**
     * @param mixed $appId
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @param null $encodingAESKey
     */
    public function setEncodingAESKey($encodingAESKey)
    {
        $this->encodingAESKey = $encodingAESKey;
    }

    /**
     * @param null $encodingOldAESKey
     */
    public function setEncodingOldAESKey($encodingOldAESKey)
    {
        $this->encodingOldAESKey = $encodingOldAESKey;
    }

    /**
     * @param null $payKey
     */
    public function setPayKey($payKey)
    {
        $this->payKey = $payKey;
    }

    /**
     * @param mixed $mchId
     */
    public function setMchId($mchId)
    {
        $this->mchId = $mchId;
    }

    /**
     * @param mixed $appSecrect
     */
    public function setAppSecret($appSecrect)
    {
        $this->appSecret = $appSecrect;
    }


    /**
     * @return mixed
     */
    public function getLocalAppId()
    {
        return $this->localAppId;
    }

    /**
     * @param mixed $localAppId
     */
    public function setLocalAppId($localAppId)
    {
        $this->localAppId = $localAppId;
    }

}