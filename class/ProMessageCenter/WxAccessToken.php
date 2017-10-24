<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/8
 * Time: 13:07
 */
class ProMessageCenter_WxAccessToken extends ProMessageCenter_WxConsoleBase
{
    const ACCESS_TOKEN = 'accessToken';


    /**
     * @param $appId
     * @return string $access_token
     * @throws Exception
     */
    public function getAccessToken($appId)
    {
        $key = self::getAccessTokenKey($appId);
        $wxConsoleData = WxDrive_ConsoleData::getInstance();
        $result = $wxConsoleData->get($key);
        if (!empty($result[$key])) {
            //存在，且未过期
            $result = Model::deCode($result[$key], 1);
            if ($result['expires_in'] >= time()) {
                return $result['access_token'];
            }else{
            	$wxConsoleData->del($key);
            }
        }
        /**
         * 增加文件锁,
         * 避免多次更新
         */
        $path = ROOT . 'log/mc/';
        mkdirs($path);
        $fileName = $path . $key;
        $file = fopen($fileName, "w+");
        if (!flock($file, LOCK_EX | LOCK_EX)) {
            throw new Exception('getAccessToken 加锁失败', -500);
        }
        try {
            $accessToken = $wxConsoleData->get($key);
            if (empty($accessToken[$key])) {
                $wxDriveConsole = new WxDrive_Console($this->getConfig());
                $accessToken = $wxDriveConsole->getAccessToken();
                $wxConsoleData->set(Model::enCode($accessToken), $key);
            } else {
                $accessToken = Model::deCode($accessToken[$key], 1);
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
        return $accessToken['access_token'];
    }


    public static function getAccessTokenKey($appId)
    {
        return self::ACCESS_TOKEN . $appId;
    }


}