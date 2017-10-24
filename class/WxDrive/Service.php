<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/13
 * Time: 12:36
 */
class WxDrive_Service extends WxDrive_Console
{

    const URL_ADD_SERVICE = 'https://api.weixin.qq.com/customservice/kfaccount/add';
    const URL_UPDATE_SERVICE = 'https://api.weixin.qq.com/customservice/kfaccount/update';
    const URL_DELETE_SERVICE = 'https://api.weixin.qq.com/customservice/kfaccount/del';
    const URL_UPLOADHEADIMG_SERVICE = 'http://api.weixin.qq.com/customservice/kfaccount/uploadheadimg';
    const URL_KF_SERVICE = 'https://api.weixin.qq.com/cgi-bin/customservice/getkflist';


    /**
     * 添加客服
     * @param $accessToken
     * @param $account  帐号 test@test 格式
     * @param $name     昵称
     * @param $password 密码
     * @return mixed
     */
    public function add($accessToken, $account, $name, $password)
    {
        $data = array(
            "kf_account" => $account,
            "nickname" => $name,
            "password" => md5($password)
        );

        $url = self::URL_ADD_SERVICE . '?' . http_build_query(array(
                'access_token' => $accessToken
            ));
        $result = WxDrive_Base::curlPostJson($url, $data);

        return $result;
    }


    /**
     * 修改客服
     * @param $accessToken
     * @param $account  帐号 test@test 格式
     * @param $name     昵称
     * @param $password 密码
     * @return mixed
     */
    public function update($accessToken, $account, $name, $password)
    {
        $data = array(
            "kf_account" => $account,
            "nickname" => $name,
            "password" => md5($password)
        );

        $url = self::URL_UPDATE_SERVICE . '?' . http_build_query(array(
                'access_token' => $accessToken
            ));
        $result = WxDrive_Base::curlPostJson($url, $data);

        return $result;
    }

    /**
     * 删除客服
     * @param $accessToken
     * @param $account  帐号 test@test 格式
     * @param $name     昵称
     * @param $password 密码
     * @return mixed
     */
    public function del($accessToken, $account, $name, $password)
    {
        $data = array(
            "kf_account" => $account,
            "nickname" => $name,
            "password" => md5($password)
        );

        $url = self::URL_DELETE_SERVICE . '?' . http_build_query(array(
                'access_token' => $accessToken
            ));
        $result = WxDrive_Base::curlPostJson($url, $data);
        return $result;
    }

    /**
     * 设置客服头像
     * @param $accessToken
     * @param $account  帐号 test@test 格式
     * @param $file     文件名
     * @return mixed
     */
    public function uploadHeadImg($accessToken, $account, $file)
    {
        $url = self::URL_UPLOADHEADIMG_SERVICE . '?' . http_build_query(array(
                'access_token' => $accessToken,
                "kf_account" => $account
            ));
        $result = WxDrive_Base::curlPostFile($url, $file);
        return $result;
    }


    public function getList($accessToken)
    {
        $url = self::URL_KF_SERVICE . '?' . http_build_query(array(
                'access_token' => $accessToken
            ));
        $result = WxDrive_Base::curlGet($url);
        return $result;
    }


}