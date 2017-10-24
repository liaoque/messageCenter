<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/8
 * Time: 13:07
 */
class ProMessageCenter_WxJsdk extends ProMessageCenter_WxConsoleBase
{

    const JSAPI_TICKET_KEY = 'jsdkTicket';

    private $drive = null;

    /**
     * @return null|WxDrive_Jsdk
     */
    public function getDrive()
    {
        return $this->drive;
    }

    public function __construct(WxDrive_Config $config)
    {
        parent::__construct($config);
        $this->drive = new WxDrive_Jsdk($config);
    }


    public function create($url)
    {
        $jsapiTicket = $this->getJsApiTicket();
        return $this->getDrive()->init($jsapiTicket, $url);
    }

    public static function getJsApiTicketKey($appId)
    {
        return self::JSAPI_TICKET_KEY . $appId;
    }

    private function getJsApiTicket()
    {
        $config = $this->getConfig();
        $wxConsoleData = WxDrive_ConsoleData::getInstance();
        $appId = $config->getLocalAppId();
        $key = self::getJsApiTicketKey($appId);
        $result = $wxConsoleData->get($key);
        if (!empty($result[$key])) {
            $result = json_decode($result[$key], 1);
            if ($result['expires_in'] >= time()) {
                return $result['ticket'];
            }else{
                $wxConsoleData->del($key);
            }
        }
        $path = ROOT . '/log/mc/';
        mkdirs($path);
        $fileName = $path . $key;
        $file = fopen($fileName, "w+");
        if (!flock($file, LOCK_EX | LOCK_EX)) {
            throw new Exception(-500, 'getJsApiTicket 加锁失败');
        }
        try {
            $result = $wxConsoleData->get($key);
            if (empty($result[$key])) {
                $proMessageCenterWxAccessToken = new ProMessageCenter_WxAccessToken($config);
                $accessToken = $proMessageCenterWxAccessToken->getAccessToken($appId);
                $result = $this->getDrive()->getJsApiTicket($accessToken);
                $wxConsoleData->set(Model::enCode($result), $key);
            } else {
                $result = Model::deCode($result[$key], 1);
            }
        } catch (Exception $e) {
            throw $e;
        } finally {
            /**
             * 不管执行成功没有,
             * 都取消文件锁,
             * 并关闭文件句柄
             * 取消文件锁
             */
            flock($file, LOCK_UN);
            fclose($file);
        }
        return $result['ticket'];
    }


}