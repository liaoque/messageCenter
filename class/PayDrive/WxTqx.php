<?php

/**
 * 微信星启天
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/1
 * Time: 13:48
 */
class PayDrive_WxTqx implements PayDrive_PayInterface
{

    const PAY_TYPE_SYS = '35';

    const PAY_TYPE_OUT = "13";

    public function createUrl($order, $gid = 0)
    {
//        XqtPay.mhtOrderNo
//        XqtPay.payChannelType
//        XqtPay.consumerId
//        XqtPay.mhtOrderName
//        XqtPay.mhtOrderDetail
//        XqtPay.mhtOrderAmt
//        XqtPay.notifyUrl
//        XqtPay.superid
//        XqtPay.sign

        global $_SC;
        // 支付类型
        $payment_type = "13";
        // 必填，不能修改
        // 服务器异步通知页面路径
        $noticeurl = $_SC['pay'] . "/index/xingqt/notify/";

        // 商户订单号
        $out_trade_no = $order['sn'];
        // 商户网站订单系统中唯一订单号，必填

        // 订单名称
        $subject = $order['sn'];
        // 必填
        // 付款金额
        $total_fee = $order['amount'] * 100;
        if($_POST['gid'] == 85){
            $total_fee = 1;
        }
        // 必填
        // 订单描述
        $body = $order['body'];

        $key = $_SC['xingqt_config']['key'];

        $customerid = $_SC['xingqt_config']['customerid'];


        $data = array(
            "mhtOrderNo" => $out_trade_no,
            "payChannelType" => $payment_type,
            "consumerId" => $customerid,
            "mhtOrderName" => $subject,
            "mhtOrderDetail" => $body,
            "mhtOrderAmt" => $total_fee,
            "notifyUrl" => $noticeurl,
            "superid" => "100000",

            //该签名是订单签名
            "pay_sign" => strtoupper(md5("customerid={$customerid}&sdcustomno={$out_trade_no}&orderAmount={$total_fee}" . $key))
        );
        return $data;
    }

    public function counteractKxd($uid, $gid, $kxd, $sn)
    {
        $phoneSiteKxd = new PhoneSite_Kxd();
        return $phoneSiteKxd->counteractKxd($uid, $gid, $kxd, $sn);
    }

}