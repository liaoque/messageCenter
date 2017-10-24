<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/26
 * Time: 20:13
 */
class ProMessageCenter_Code
{

    const TYPE_NUM = 1;
    const TYPE_EN = 2;
    const DEFAULT_APP_ID = 1;

    const KEY_TYPE_PHONE = 3;
    const KEY_TYPE_MAIL = 2;
    const KEY_TYPE_PIC = 1;

    const CODE_KEY = 'c:';
    const CODE_PKKEY = 'codeInc';

    private $type = self::TYPE_NUM;
    private $redis;
    private $len = 4;
    private $code = null;
    private $timeOut = 120;

    public function __construct()
    {
        $this->redis = PhpRedis::getInstance();
    }

    public function getType()
    {
        return $this->type;
    }

    public function getCode()
    {
        return $this->code;
    }


    public function getLen()
    {
        return $this->len;
    }

    public function getTimeOut()
    {
        return $this->timeOut;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function setLen($len)
    {
        $this->len = $len;
        return $this;
    }

    public function setTimeOut($timeOut)
    {
        $this->timeOut = $timeOut;
        return $this;
    }

    /**
     * 创建验证码
     * @param int $type
     * @param int $len
     * @return string
     */
    private function createCode($type = self::TYPE_NUM, $len = 4)
    {
        switch ($type) {
            case self::TYPE_NUM:
                $code = '0123456789';
                break;
            case self::TYPE_EN:
                $code = '0123456789QWERTYUIOPASDFGHJKLZXCVBNM';
                break;
        }
        $code = str_shuffle($code);
        return $this->code = substr($code, 0, $len);
    }

    /**
     * 验证码自增key
     * @return int
     */
    public static function getCodePkId()
    {
        $redis = PhpRedis::getInstance();
        return $redis->incr(self::CODE_PKKEY) . 'N' . date('YmdHis');
    }

    /**
     * 获取验证码key
     * @param $pkId
     * @param int $appId
     * @return string
     * 'c:a:1:pk:1'
     * 'c:a:appId:pk:pkId'
     */
    public static function getCodeKey($pkId, $appId = self::DEFAULT_APP_ID)
    {
        return self::CODE_KEY . 'a:' . $appId . ':pk:' . $pkId;
    }

    public static function getCodeKeyMail($pkId, $appId = self::DEFAULT_APP_ID)
    {
        return self::CODE_KEY . 'm:a:' . $appId . ':pk:' . $pkId;
    }

    public static function getCodeKeyPhone($pkId, $appId = self::DEFAULT_APP_ID)
    {
        return self::CODE_KEY . 'p:a:' . $appId . ':pk:' . $pkId;
    }


    /**
     * 获取普通验证码
     * @return int 返回code
     */
    public function makeCode()
    {
        $code = $this->createCode($this->getType(), $this->getLen());
        return $code;
    }

    /**
     * 校验验证码
     * @param $pkId 返回凭证Id
     * @param $code 验证码
     * @param int $appId 应用Id
     *  @param int $type 1, 2, 3
     * @return bool
     */
    public static function checkCode($pkId, $code, $appId = self::DEFAULT_APP_ID, $type = self::KEY_TYPE_MAIL)
    {
        switch ($type) {
            case self::KEY_TYPE_MAIL:
                $key = self::getCodeKeyMail($pkId, $appId);
                break;
            case self::KEY_TYPE_PHONE:
                $key = self::getCodeKeyPhone($pkId, $appId);
                break;
            default:
                $key = self::getCodeKey($pkId, $appId);
                break;
        }
        $code2 = PhpRedis::getInstance()->get($key);
        if (!$code2) {
            return null;
        }
        return strtoupper($code2) == $code;
    }

    /**
     * 发送邮箱验证码
     * @param ProMessageCenter_Message $message 消息对象
     * @return int
     * new ProMessageCenter_Email(1, 1, []);
     *
     */
    public function sendEmailCode(ProMessageCenter_Message $message)
    {
        $code = $this->makeCode($message->getAppId());
        if ($code) {
            $pkId = $message->setContent([
                'code' => $code
            ])->send(Model::factoryCreate('ProMessageCenter_EmailQueue'));
            if ($pkId) {
                $key = $this->getCodeKeyMail($pkId, $message->getAppId());
                return $this->redis->set($key, $code, $this->getTimeOut()) ? $pkId : false;
            }
        }
        return false;
    }

    /**
     * 发送短信验证码
     * @param ProMessageCenter_Message $message 消息对象
     * @return int
     */
    public function sendSmsCode(ProMessageCenter_Message $message)
    {
        $code = $this->makeCode($message->getAppId());
        if ($code) {
            $pkId = $message->setContent([
                'code' => $code,
                'timeOut' => $this->getTimeOut() / 60,
            ])->send(Model::factoryCreate('ProMessageCenter_SmsQueue'));
            if ($pkId) {
                $key = $this->getCodeKeyPhone($pkId, $message->getAppId());
                return $this->redis->set($key, $code, $this->getTimeOut()) ? $pkId : false;
            }
        }
        return $pkId;
    }

}