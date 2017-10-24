<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/1
 * Time: 13:52
 */
Interface PayDrive_InterfacePay
{
    public function createPayRequest(PayDrive_OrderData $order, $ext = []);

//    public function counteractKxd($uid, $gid, $kxd, $sn);
}