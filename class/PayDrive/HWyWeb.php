<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/28
 * Time: 19:21
 */
//汇付宝网银
class PayDrive_HWyWeb extends PayDrive_PayBase implements PayDrive_InterfacePay
{

    const PAY_TYPE_OUT = "20";

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
        $agent_bill_id = $sn = strtolower($order->getSn());

        //订单总金额不可为空，单位：元，小数点后保留两位。12.37
        $amount = $order->getAmount();
        $pay_amt = sprintf('%0.2f', $amount);

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
        $goods_name = iconv('utf-8', 'GB2312',$order->getProductName($this->config->getTitle()));

        //产品数量,长度最长20字符，大于等于1
        $goods_num = $order->getNum();

        //商户自定义原样返回,长度最长50字符
        $remark = md5($agent_bill_id);

        //支付说明, 长度50字符
        $goods_note = iconv('utf-8', 'GB2312', $order->getDesc());

        $key = $this->config->getAppKey();


        $signStr = '';
        $signStr = $signStr . 'version=' . $version;
        $signStr = $signStr . '&agent_id=' . $agent_id;
        $signStr = $signStr . '&agent_bill_id=' . $agent_bill_id;
        $signStr = $signStr . '&agent_bill_time=' . $agent_bill_time;
        $signStr = $signStr . '&pay_type=' . $pay_type;
        $signStr = $signStr . '&pay_amt=' . $pay_amt;
        $signStr = $signStr . '&notify_url=' . $notify_url;
        $signStr = $signStr . '&return_url=' . $return_url;
        $signStr = $signStr . '&user_ip=' . $user_ip;
        $signStr = $signStr . '&key=' . $key;
        $pay_code = 0;
        // 获取sign密钥
        $sign = md5($signStr);
        $signStr = strtolower($signStr);
        $goods_name = urlencode($goods_name);
        $goods_num = urlencode($goods_num);
        $goods_note = urlencode($goods_note);
        $remark = urlencode($remark);
        $html = "<textarea style='display:none;' name=\"cardData\" id=\"cardData\" rows=\"10\" cols=\"70\">签名原始数据：{$signStr}</textarea>
<textarea style='display:none;' name=\"sign\" id=\"sign\" rows=\"10\" cols=\"70\">签名加密后数据数据：{$sign}</textarea>
<form id='frmSubmit' method='post' name='frmSubmit' action='https://pay.Heepay.com/Payment/Index.aspx' style='display:none;'>
<input type='text' name='version' value='{$version}' />
<input type='text' name='agent_id' value='{$agent_id}' />
<input type='text' name='agent_bill_id' value='{$agent_bill_id}' />
<input type='text' name='agent_bill_time' value='{$agent_bill_time}' />
<input type='text' name='pay_type' value='{$pay_type}' />
<input type='text' name='pay_code' value='{$pay_code}' />
<input type='text' name='pay_amt' value='{$pay_amt}' />
<input type='text' name='notify_url' value='{$notify_url}' />
<input type='text' name='return_url' value='{$return_url}' />
<input type='text' name='user_ip' value='{$user_ip}' />
<input type='text' name='goods_name' value='{$goods_name}' />
<input type='text' name='goods_num' value='{$goods_num}' />
<input type='text' name='goods_note' value='{$goods_note}' />
<input type='text' name='remark' value='{$remark}' />
<input type='text' name='sign' value='{$sign}' />
<input type='submit' value='提交' />
</form>
<script language='javascript'>
document.frmSubmit.submit();
</script>";
        return $html;
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
