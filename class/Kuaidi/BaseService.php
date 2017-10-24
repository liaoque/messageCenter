<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/31
 * Time: 14:33
 */
interface Kuaidi_BaseService
{
    public function subscribe(Kuaidi_Data $kuaiDiInfo);
    public function publish(Kuaidi_Data $data, $appInfo, $ext = array());
    public function getOfCurl(Kuaidi_Data $kuaiDiInfo);
    public function notify($data);
}