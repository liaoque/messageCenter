<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/9
 * Time: 8:56
 */
class PayDrive_Apple extends PayDrive_PayBase implements PayDrive_InterfacePay
{
    const PAY_TYPE_SYS = '39';

    //苹果不需要
    const PAY_TYPE_OUT = "0";

    const PAY_URL = '';

    private $config;

    public function __construct()
    {
        global $_SC;
        $this->config = $_SC['heepay_config'];
    }

    public function createPayRequest(PayDrive_OrderData $order, $ext = [])
    {
        return array(
            'sn' => strtolower($order->getSn())
        );
    }

    public function parseResult($result)
    {
        if (strpos($result, 'error') != false) {
            return false;
        }
        $xml = simplexml_load_string($result);
        return array('token_id' => (string)$xml);
    }

}