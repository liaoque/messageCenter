<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/28
 * Time: 19:21
 */
//汇付宝网银
class PayDrive_HWySdk extends PayDrive_PayBase implements PayDrive_InterfacePay
{

    const PAY_TYPE_OUT = "18";

    const PAY_URL = 'https://pay.heepay.com/Phone/SDK/PayInit.aspx';


    public function createPayRequest(PayDrive_OrderData $order, $ext = [])
    {
        /**
         * *************************请求参数*************************
         */

        //当前接口版本号1（话费通卡为2）
        $version = 1;

        //支付类型
        $pay_type = self::PAY_TYPE_OUT;

        //商户编号如1001
        $agent_id = $this->config->getPartner();

        //商户系统内部的定单号（要保证唯一）。长度最长50字符
        $agent_bill_id = strtolower($order->getSn());

        //订单总金额不可为空，单位：元，小数点后保留两位。12.37
        $pay_amt = sprintf('%0.2f', $order->getAmount());

        //签名时不包括此参数，明文必传，可以填任意url，只做校验
//        $return_url = $this->config->getReturnUrl();
        $return_url = $order->getReturnUrl();

        //支付后返回的商户处理页面，URL参数是以http://或https://开头的完整URL地址(后台处理)
//        $notify_url = $this->config->getNotifyUrl();
        $notify_url = $order->getNotifyUrl();

        //用户所在客户端的真实ip。
        $user_ip = $order->getIp();

        //提交单据的时间yyyyMMddHHmmss20100225102000
        $agent_bill_time = $order->getCreateTime('YmdHis');

        //商品名称,长度最长50字符, (不可为空)
        $goods_name = iconv('utf-8', 'GB2312',$order->getProductName($agent_bill_id));

        //产品数量,长度最长20字符，大于等于1
        $goods_num = $order->getNum();

        //商户自定义原样返回,长度最长50字符
        $remark = md5($agent_bill_id);

        //支付说明, 长度50字符
        $goods_note = iconv('utf-8', 'GB2312', $order->getDesc());

        $key = $this->config->getPartnerKey();

        $postDate = array(
            'version' => $version,
            'agent_id' => $agent_id,
            'agent_bill_id' => $agent_bill_id,
            'agent_bill_time' => $agent_bill_time,
            'pay_type' => $pay_type,
            'pay_amt' => $pay_amt,
            'notify_url' => $notify_url,
            'user_ip' => $user_ip,
            'key' => $key
        );

        $sign = '';
        foreach ($postDate as $k => $v) {
            $sign .= $k . '=' . $v . '&';
        }
        $postDate['sign'] = strtolower(md5(substr($sign, 0, -1)));
        $postDate['return_url'] = $return_url;
        $postDate['goods_name'] = $goods_name;
        $postDate['goods_num'] = $goods_num;
        $postDate['remark'] = $remark;
        $postDate['goods_note'] = $goods_note;


        $query = http_build_query($postDate);
        $url = self::PAY_URL . '?' . $query;
        $query = new Http_RequestBase();
        $result = $query->curlGet($url);
        $data = $this->parseResult($result);
        if ($data) {
            $data['agent_id'] = $agent_id;
            $data['bill_no'] = $agent_bill_id;
        }
        return $data;
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
