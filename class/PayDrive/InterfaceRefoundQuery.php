<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/1
 * Time: 13:52
 */
Interface PayDrive_InterfaceRefoundQuery
{
    public function refundQuery(PayDrive_OrderData $order);

//    static public function sendResult($code, $mes = null);
}