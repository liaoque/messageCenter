<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/28
 * Time: 19:21
 */
class PayDrive_HZfbWeb extends PayDrive_PayBase implements PayDrive_InterfacePay
{

    const PAY_TYPE_OUT = "22";

    const PAY_URL = 'https://pay.heepay.com/Phone/SDK/PayInit.aspx';



    /**
     * @param PayDrive_OrderData $order
     * @return array|bool
     */
    public function createPayRequest(PayDrive_OrderData $order, $ext = [])
    {
        /**
         * *************************请求参数*************************
         */
        return false;
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
