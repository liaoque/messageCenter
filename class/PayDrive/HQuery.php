<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/28
 * Time: 19:21
 */
//汇付宝网银
class PayDrive_HQuery extends PayDrive_AlipayPayBase implements PayDrive_InterfaceQuery
{

    const QUERY_URL = 'https://query.heepay.com/Payment/Query.aspx';


    public function query(PayDrive_OrderData $order)
    {
//        version	必填	版本号1
//        agent_id	必填	商家数字帐号1234567
//        agent_bill_id	必填	商户系统内部的定单号, 长度最长50字符
//        agent_bill_time	必填	单据的时间yyyyMMddHHmmss 20091112102000
//        return_mode	必填	查询结果返回类型1=字符串返回
//        remark	必填	商家数据包，原样返回，长度最长50字符
//        sign_type	必填	签名方式： RSA，SHA256，MD5
//        sign	必填	MD5签名结果
        $version = 1;
        $agent_id = $this->config->getPartner();
        $agent_bill_id = $sn = strtolower($order->getSn());
        $agent_bill_time = $order->getCreateTime('YmdHis');
        $return_mode = 1;
        $remark = md5($agent_bill_id);
        $sign_type = 'MD5';
        $key = $this->config->getAppKey();
//        sign=md5(version=1&agent_id=1234567&agent_bill_id=201002251422231234&agent_bill_time=20091112102000&return_mode=1&key=123456)
        $str = "version=$version&agent_id=$agent_id&agent_bill_id=$agent_bill_id&agent_bill_time=$agent_bill_time&return_mode=$return_mode&key=$key";
        $sign = md5($str);
        $data = [
            'version' => $version,
            'agent_id' => $agent_id,
            'agent_bill_id' => $agent_bill_id,
            'agent_bill_time' => $agent_bill_time,
            'return_mode' => $return_mode,
            'remark' => $remark,
            'sign_type' => $sign_type,
            'sign' => $sign,
        ];
        $result = Model::factoryCreate('Http_RequestBase')->curlPost(self::QUERY_URL, $data);
//        agent_id=1001|agent_bill_id=2005032001234|jnet_bill_no=B070606017329737|pay_type=10|result=1|pay_amt=12.01|pay_message=test|remark=test_remark|sign=6f8fb4aeeafac5820979a86f0d2d1300



        return $result;
    }


}
