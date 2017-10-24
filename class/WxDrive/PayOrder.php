<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/18
 * Time: 11:14
 */
class WxDrive_PayOrder
{

    const PAY_URL = 'https://api.mch.weixin.qq.com/pay/unifiedorder';

    private static $feeType = 'CNY';


//     符合ISO 4217标准的三位字母代码，默认人民币：CNY，其他值列表详见货币类型  https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=4_2
    public static function setFeeType($type)
    {
        self::$feeType = $type;
    }

    public static function getFeeType()
    {
        return self::$feeType;
    }

    /**
     * @param $payData WxDrive_WeiXinPayData 对象
     * @return array|mixed|null|stdClass
     */
    private function create($payData)
    {

//      1必填 0可选
//      1  微信分配的公众账号ID（企业号corpid即为此appId）
        $appid = $payData->getAppId();
//      1  微信支付分配的商户号
        $mch_id = $payData->getMchId();
//      0  终端设备号(门店号或收银设备ID)，注意：PC网页或公众号内支付请传"WEB"
        $device_info = $payData->getDeviceInfo();

//      1  随机字符串，不长于32位。推荐随机数生成算法 https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=4_3
        $nonce_str = $payData->getNonceStr();

//      0  签名类型，目前支持HMAC-SHA256和MD5，默认为MD5
        $sign_type = $payData->getSignType();

//      1  商品简单描述，该字段须严格按照规范传递，具体请见参数规定
        $body = $payData->getBody();

//      0  商品详细列表，使用Json格式，传输签名前请务必使用CDATA标签将JSON文本串保护起来。
        $detail = $payData->getDetail();

//      0  附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
        $attach = $payData->getAttach();

//      1  商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
        $out_trade_no = $payData->getOutTradeNo();

//      0  符合ISO 4217标准的三位字母代码，默认人民币：CNY，其他值列表详见货币类型  https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=4_2
        $fee_type = $payData->getFeeType();

//      1  订单总金额，单位为分，详见支付金额 https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=4_2
        $total_fee = $payData->getTotalFee();

//      1  APP和网页支付提交用户端ip，Native支付填调用微信支付API的机器IP。//JSAPI--公众号支付、NATIVE--原生扫码支付、APP--app支付，MICROPAY--刷卡支付 刷卡支付有单独的支付接口，不调用统一下单接口
        $spbill_create_ip = $payData->getSpbillCreateIp();


//      0  订单生成时间，格式为yyyyMMddHHmmss，如2009年12月25日9点10分10秒表示为20091225091010。
        $time_start = $payData->getTimeStart();

//      0  订单失效时间，格式为yyyyMMddHHmmss，如2009年12月27日9点10分10秒表示为20091227091010。
        $time_expire = $payData->getTimeExpire();

//      0  商品标记，代金券或立减优惠功能的参数， https://pay.weixin.qq.com/wiki/doc/api/tools/sp_coupon.php?chapter=12_1
        $goods_tag = $payData->getGoodsTag();

//      1  接收微信支付异步通知回调地址，通知url必须为直接可访问的url，不能携带参数
        $notify_url = $payData->getNotifyUrl();

//      1  取值如下：JSAPI，NATIVE，APP，详细说明见参数规定
        $trade_type = $payData->getTradeType();

//      1/0   trade_type=NATIVE，此参数必传。此id为二维码中包含的商品ID，商户自行定义。
        $product_id = $payData->getProductId();

//      0  no_credit--指定不能使用信用卡支付
        $limit_pay = $payData->getLimitPay();

//      1/0  trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识。
//        企业号请使用【企业号OAuth2.0接口】获取企业号内成员userid，再调用【企业号userid转openid接口】进行转换
        $openid = $payData->getOpenId();

//      该字段用于上报支付的场景信息,针对H5支付有以下三种场景,请根据对应场景上报,H5支付不建议在APP端使用，针对场景1，2请接入APP支付，不然可能会出现兼容性问题
        $sceneInfo = $payData->getSceneInfo();

        $data = array(
            'appid' => $appid,
            'mch_id' => $mch_id,
            'device_info' => $device_info,
            'nonce_str' => $nonce_str,
            'sign_type' => $sign_type,
            'body' => $body,
            'detail' => $detail,
            'attach' => $attach,
            'out_trade_no' => $out_trade_no,
            'fee_type' => $fee_type,
            'total_fee' => $total_fee,
            'spbill_create_ip' => $spbill_create_ip,
            'time_start' => $time_start,
            'time_expire' => $time_expire,
            'goods_tag' => $goods_tag,
            'notify_url' => $notify_url,
            'trade_type' => $trade_type,
            'product_id' => $product_id,
            'limit_pay' => $limit_pay,
            'scene_info' => $sceneInfo,
            'openid' => $openid
        );
        $key = $payData->getKey();
        $result = WxDrive_Base::createWordbookSignFilterEmpty($data, $key);
        $result['data']['sign'] = $result['sign'];
        $signData = $result['data'];
        unset($result);

        $xml = $this->arrayToXml($signData);
        $result = WxDrive_Base::curlHttpsPostXml(self::PAY_URL, $xml);
        $result = WxDrive_Base::parseXml($result);


        if ($result['return_code'] == 'FAIL') {
            throw new Exception($result['return_msg'], PayDrive_PayException::ERROR_REQUEST_PAY);
        }
        if ($result['result_code'] == 'FAIL') {
            throw new Exception($result['return_msg'], PayDrive_PayException::ERROR_REQUEST_PAY);
        }
        return $result;
    }


    private function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if ($key != 'detail') {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }


    /**
     * @param array $info
     *          title
     *          order_number 本地订单号
     *          amount     单位是分
     *          notifyUrl  回调url
     *          productId  商品id
     * @return array
     *          code 1成功 2失败
     *          mes 错误信息
     *          payToken 支付令牌
     *          qrCodeUrl 二维码连接
     */
    public function qrCode($payData)
    {

        $payData->setSpbillCreateIp(get_client_ip());
//        $payData->setDeviceInfo('WEB');
        $payData->setTradeType('NATIVE');
        $result = $this->create($payData);
        if ($result['code'] == 2) {
            return $result;
        }
        return array(
            'code' => '1',
            'payToken' => $result['prepay_id'],
            'qrCodeUrl' => $result['code_url']
        );
    }

    /**
     * @param array $payData WxDrive_WeiXinPayData对象
     *          title
     *          order_number 本地订单号
     *          amount     单位是分
     *          notifyUrl  回调url
     *          openid     商户appid下的唯一标识
     * @return array
     *          code 1成功 2失败
     *          mes 错误信息
     *          payToken 支付令牌
     */
    public function JSDK($payData)
    {
        $payData->setSpbillCreateIp(get_client_ip());
        $payData->setDeviceInfo('WEB');
        $payData->setTradeType('JSAPI');
        $result = $this->create($payData);
        if ($result['code'] == 2) {
            return $result;
        }
        return array(
            'code' => '1',
            'payToken' => $result['prepay_id']
        );
    }

    /**
     * @param array $payData WxDrive_WeiXinPayData对象
     *          title
     *          order_number 本地订单号
     *          amount     单位是分
     *          notifyUrl  回调url
     * @return array
     *          code 1成功 2失败
     *          mes 错误信息
     *          payToken 支付令牌
     */
    public function App($payData)
    {
        $payData->setSpbillCreateIp(get_client_ip());
        $payData->setTradeType('APP');
        $result = $this->create($payData);
        if ($result['code'] == 2) {
            return $result;
        }

        $data = array(
            //微信开放平台审核通过的应用APPID
            'appid' => $result['appid'],
            //微信支付分配的商户号
            'partnerid' => $result['mch_id'],
            //预支付交易会话ID
            'prepayid' => $result['prepay_id'],
            //扩展字段
            'package' => 'Sign=WXPa',
            //随机字符串
            'noncestr' => WxDrive_Base::createNonceStr(),
            //时间戳
            'timestamp' => time()
        );
        $key = $payData->getKey();
        $result = WxDrive_Base::createWordbookSignFilterEmpty($data, $key);
        $data['sign'] = $result['sign'];
        return $data;
    }

    /**
     * @param array $payData WxDrive_WeiXinPayData对象
     *          title
     *          order_number 本地订单号
     *          amount     单位是分
     *          notifyUrl  回调url
     * @return array
     *          code 1成功 2失败
     *          mes 错误信息
     *          payToken 支付令牌
     *          mwebUrl 跳转url
     */
    public function h5($payData)
    {
        $payData->setSpbillCreateIp(get_client_ip());
        $payData->setTradeType('MWEB');
        $result = $this->create($payData);
        if ($result['code'] == 2) {
            return $result;
        }
        return array(
            'code' => '1',
            'payToken' => $result['prepay_id'],
            'mwebUrl' => $result['mweb_url']
        );
    }


    /**
     * @param $payData WxDrive_WeiXinPayData对象
     * @param $xml 可传值, string或者数组
     * @return array
     */
    public function notify($payData, $xml)
    {
        $result = is_array($xml) ? $xml : WxDrive_Base::parseXml($xml);

        if ($result['return_code'] != 'SUCCESS') {
            throw new PayDrive_PayException($result['return_msg'], PayDrive_PayException::ERROR_OTHER);
        }
        if ($result['result_code'] != 'SUCCESS') {
            throw new PayDrive_PayException($result['err_code_des'], PayDrive_PayException::ERROR_OTHER);
        }
        /*校验签名*/
        $key = $payData->getKey();
        $sign = $result['sign'];
        unset($result['sign']);
        $signData = WxDrive_Base::createWordbookSignFilterEmpty($result, $key);
        if ($signData['sign'] != $sign) {
            throw new PayDrive_PayException('签名错误', PayDrive_PayException::ERROR_OTHER_SIGN);
        }
        return array(
            'order' => $result['transaction_id'],
            'sn' => $result['out_trade_no'],
        );
    }

    /**
     * 查询订单接口
     * @param $payData WxDrive_WeiXinPayData 对象
     * @param int $timeOut
     * @return array|mixed|null|stdClass
     */
    public function query($payData, $timeOut = 6)
    {
        $url = "https://api.mch.weixin.qq.com/pay/orderquery";
        $appid = $payData->getAppId();
        $mch_id = $payData->getMchId();
        $out_trade_no = $payData->getOutTradeNo();
        $nonce_str = $payData->getNonceStr();
        $key = $payData->getKey();;
        $data = array(
            'appid' => $appid,
            'mch_id' => $mch_id,
            'out_trade_no' => $out_trade_no,
            'nonce_str' => $nonce_str
        );
        $result = WxDrive_Base::createWordbookSignFilterEmpty($data, $key);
        $result['data']['sign'] = $result['sign'];
        $signData = $result['data'];
        unset($result);
        $xml = $this->arrayToXml($signData);
        $result = WxDrive_Base::curlHttpsPostXml($url, $xml, $timeOut);
        $result = WxDrive_Base::parseXml($result);
        return $result;
    }

    public function close($payData, $timeOut = 6)
    {
        $url = 'https://api.mch.weixin.qq.com/pay/closeorder';
        $appid = $payData->getAppId();
        $mch_id = $payData->getMchId();
        $out_trade_no = $payData->getOutTradeNo();
        $nonce_str = $payData->getNonceStr();
        $key = $payData->getKey();;
        $data = array(
            'appid' => $appid,
            'mch_id' => $mch_id,
            'out_trade_no' => $out_trade_no,
            'nonce_str' => $nonce_str
        );
        $result = WxDrive_Base::createWordbookSignFilterEmpty($data, $key);
        $result['data']['sign'] = $result['sign'];
        $signData = $result['data'];
        unset($result);
        $xml = $this->arrayToXml($signData);
        $result = WxDrive_Base::curlHttpsPostXml($url, $xml, $timeOut);
        $result = WxDrive_Base::parseXml($result);
        return $result;
    }


    public function refund($payData, $timeOut = 6)
    {
        $url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
        $appid = $payData->getAppId();
        $mch_id = $payData->getMchId();
        $out_trade_no = $payData->getOutTradeNo();
        $out_refund_no = $payData->getOutRefundNo();
        $nonce_str = $payData->getNonceStr();
        $key = $payData->getKey();;
        $refund_fee = $payData->getRefundFee();

        $data = array(
            'appid' => $appid,
            'mch_id' => $mch_id,
            'out_trade_no' => $out_trade_no,
            'out_refund_no' => $out_refund_no,
            'total_fee' => $payData->getTotalFee(),
            'refund_fee' => $refund_fee,
            'nonce_str' => $nonce_str
        );
        $result = WxDrive_Base::createWordbookSignFilterEmpty($data, $key);
        $result['data']['sign'] = $result['sign'];
        $signData = $result['data'];
        unset($result);
        $xml = $this->arrayToXml($signData);
        $result = WxDrive_Base::curlHttpsPostXml($url, $xml, $timeOut);
        $result = WxDrive_Base::parseXml($result);
        return $result;
    }

    public function refundQuery($payData, $timeOut = 6)
    {
        $url = 'https://api.mch.weixin.qq.com/pay/refundquery';

        $appid = $payData->getAppId();
        $mch_id = $payData->getMchId();
        $out_trade_no = $payData->getOutTradeNo();
        $out_refund_no = $payData->getOutRefundNo();
        $nonce_str = $payData->getNonceStr();
        $key = $payData->getKey();;
        $data = array(
            'appid' => $appid,
            'mch_id' => $mch_id,
            'out_trade_no' => $out_trade_no,
            'out_refund_no' => $out_refund_no,
            'nonce_str' => $nonce_str
        );
        $result = WxDrive_Base::createWordbookSignFilterEmpty($data, $key);
        $result['data']['sign'] = $result['sign'];
        $signData = $result['data'];
        unset($result);
        $xml = $this->arrayToXml($signData);
        $result = WxDrive_Base::curlHttpsPostXml($url, $xml, $timeOut);
        $result = WxDrive_Base::parseXml($result);
        return $result;
    }


}