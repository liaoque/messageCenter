<?php
require_once ROOT . '/lib/ali/aop/request/AlipayTradeAppPayRequest.php';

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/16
 * Time: 11:33
 */
class PayDrive_AlipaySdkPay extends PayDrive_AlipayPayBase implements PayDrive_InterfacePay
{

    public function createPayRequest(PayDrive_OrderData $order, $ext = [])
    {
//        $notify = $this->config->getNotifyUrl();
        $notify = $order->getNotifyUrl();
        $sn = strtolower($order->getSn());
        $amount = $order->getAmount();
        $data = array(
            //对一笔交易的具体描述信息。如果是多种商品，请将商品描述字符串累加传给body。
            'body' => $order->getDesc(),
            //商品的标题/交易标题/订单标题/订单关键字等。
            'subject' => $order->getProductName($this->config->getTitle()),
            //商户网站唯一订单号
            'out_trade_no' => $sn,
            //设置未付款支付宝交易的超时时间，一旦超时，该笔交易就会自动被关闭。当用户进入支付宝收银台页面（不包括登录页面），会触发即刻创建支付宝交易，此时开始计时。取值范围：1m～15d。m-分钟，h-小时，d-天，1c-当天（1c-当天的情况下，无论交易何时创建，都在0点关闭）。 该参数数值不接受小数点， 如 1.5h，可转换为 90m。
            'timeout_express' => self::parseTime($order->getTimeOut()),
            //订单总金额，单位为元，精确到小数点后两位，取值范围[0.01,100000000]
            'total_amount' => sprintf('%0.2f', $amount),
            //销售产品码，商家和支付宝签约的产品码，为固定值QUICK_MSECURITY_PAY
            'product_code' => 'QUICK_MSECURITY_PAY'
        );

        $aop = new AopClient;
        $aop->appId = $this->config->getProduct();
        $aop->rsaPrivateKey = $this->config->getPrivateRSA2();
        $aop->signType = self::SIGN_TYPE;
        $aop->alipayrsaPublicKey = $this->config->getPublicRSA2();
        //实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.app.pay
        $request = new AlipayTradeAppPayRequest();
        //SDK已经封装掉了公共参数，这里只需要传入业务参数
        $bizcontent = json_encode($data, JSON_UNESCAPED_UNICODE);
        $request->setNotifyUrl($notify);
        $request->setBizContent($bizcontent);
        //这里和普通的接口调用不同，使用的是sdkExecute
        $response = $aop->sdkExecute($request);
        //htmlspecialchars是为了输出到页面时防止被浏览器将关键参数html转义，实际打印到日志以及http传输不会有这个问题
//        $data = htmlspecialchars($response);//就是orderString 可以直接给客户端请求，无需再做处理。
        parse_str($response, $data);
        return $data;
    }


}