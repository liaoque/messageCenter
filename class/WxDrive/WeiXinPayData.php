<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/15
 * Time: 9:25
 */
class WxDrive_WeiXinPayData
{
    //      1必填 0可选
    //      1  微信分配的公众账号ID（企业号corpid即为此appId）
    private $appId = null;

    //      1  微信支付分配的商户号
    private $mchId = null;

    //      0  终端设备号(门店号或收银设备ID)，注意：PC网页或公众号内支付请传"WEB"
    private $deviceInfo = null;

    //      1  随机字符串，不长于32位。推荐随机数生成算法 https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=4_3
    private $nonceStr = null;

    //      0  签名类型，目前支持HMAC-SHA256和MD5，默认为MD5
    private $signType = 'MD5';

    //      1  商品简单描述，该字段须严格按照规范传递，具体请见参数规定
    private $body = null;

    //      0  商品详细列表，使用Json格式，传输签名前请务必使用CDATA标签将JSON文本串保护起来。
    private $detail = null;

    //      0  附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
    private $attach = null;

    //      1  商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
    private $outTradeNo = null;

    //      0  符合ISO 4217标准的三位字母代码，默认人民币：CNY，其他值列表详见货币类型  https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=4_2
    private $feeType = 'CNY';

    //      1  订单总金额，单位为分，详见支付金额 https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=4_2
    private $totalFee = null;

    //      1  APP和网页支付提交用户端ip，Native支付填调用微信支付API的机器IP。//JSAPI--公众号支付、NATIVE--原生扫码支付、APP--app支付，MICROPAY--刷卡支付 刷卡支付有单独的支付接口，不调用统一下单接口
    private $spbillCreateIp = null;

    //      0  订单生成时间，格式为yyyyMMddHHmmss，如2009年12月25日9点10分10秒表示为20091225091010。
    private $timeStart = null;

    //      0  订单失效时间，格式为yyyyMMddHHmmss，如2009年12月27日9点10分10秒表示为20091227091010。
    private $timeExpire = null;

    //      0  商品标记，代金券或立减优惠功能的参数， https://pay.weixin.qq.com/wiki/doc/api/tools/sp_coupon.php?chapter=12_1
    private $goodsTag = null;

    //      1  接收微信支付异步通知回调地址，通知url必须为直接可访问的url，不能携带参数
    private $notifyUrl = null;

    //      1  取值如下：JSAPI，NATIVE，APP，详细说明见参数规定
    private $tradeType = null;

    //      1/0   trade_type=NATIVE，此参数必传。此id为二维码中包含的商品ID，商户自行定义。
    private $productId = null;

    //      0  no_credit--指定不能使用信用卡支付
    private $limitPay = null;

    //      1/0  trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识。
//        企业号请使用【企业号OAuth2.0接口】获取企业号内成员userid，再调用【企业号userid转openid接口】进行转换
    private $openId = null;

    //商户系统内部的退款单号，商户系统内部唯一，只能是数字、大小写字母_-|*@ ，同一退款单号多次请求只退一笔。
    private $outRefundNo = null;

    //退款总金额，订单总金额，单位为分，只能为整数，详见支付金额
    private $refundFee = null;

    //该字段用于上报支付的场景信息,针对H5支付有以下三种场景,请根据对应场景上报,H5支付不建议在APP端使用，针对场景1，2请接入APP支付，不然可能会出现兼容性问题
    private $sceneInfo = null;


    //密钥
    private $key = null;


    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        $action = substr($name, 0, 3);
        $name = lcfirst(substr($name, 3));
        if ($action == 'get') {
            $result = $this->get($name);
        } else if ($action == 'set') {
            $arguments = array_merge(array($name), $arguments);
            $result = call_user_func_array(array($this, 'set'), $arguments);
        }
        return $result;
    }

    private function get($name)
    {
        return property_exists($this, $name) && !empty($this->$name) ? $this->$name : '';
    }

    private function set($name, $value)
    {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        } else {
            return null;
        }
        return $this->$name;
    }

}