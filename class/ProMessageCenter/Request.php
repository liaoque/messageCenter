<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/4
 * Time: 13:36
 */
class ProMessageCenter_Request
{

    private $curl = null;


    public function getCurl()
    {
        if (empty($this->curl)) {
            $this->setCurl(Model::factoryCreate('Http_RequestBase'));
        }
        return $this->curl;
    }

    public function setCurl($curl)
    {
        $this->curl = $curl;
    }


    /**
     * 发送邮件接口
     * @param $appId        appId应用id
     * @param $templateId   模版id
     * @param $toMail       给谁发送
     * @param $content      内容
     * @param $key          密钥
     * @return mixed
     *      [
     *          code => 1成功 2,失败
     *          mes => 成功: '' 失败: 错误信息
     *      ]
     */
    public static function sendEmail($appId, $templateId, $toMail, $content, $key)
    {
        $request = Model::factoryCreate(__CLASS__);
        $data = [
            'appId' => $appId,
            'templateId' => $templateId,
            'toMail' => $toMail,
            'content' => is_array($content) ? Model::enCode($content, true) : $content,
            'ip' => getonlineip()
        ];
        $sign = RouteBase::createSign($data, $key);
        $data['sign'] = $sign['sign'];
        $url = ConfigPath::HOST_MESSAGE_CENTER . 'mail/index';
        $result = $request->getCurl()->curlPostJson($url, $data);
        $result2 = Model::deCode($result);
        return $result2 ? $result2 : ['code' => 2, 'mes' => '请求失败'];
    }

    public static function queryEmail($appId, $pkId, $key)
    {
        $request = Model::factoryCreate(__CLASS__);
        $data = [
            'appId' => $appId,
            'pkId' => $pkId,
            'ip' => getonlineip()
        ];
        $sign = RouteBase::createSign($data, $key);
        $data['sign'] = $sign['sign'];
        $url = ConfigPath::HOST_MESSAGE_CENTER . 'mail/query';
        $result = $request->getCurl()->curlGet($url . '?' . http_build_query($data));
        $result2 = Model::deCode($result);
        return $result2 ? $result2 : ['code' => 2, 'mes' => '请求失败'];
    }

    /**
     * 发送邮件接口
     * @param $appId        appId应用id
     * @param $templateId   模版id
     * @param $toMail       给谁发送
     * @param $content      内容
     * @param $key          密钥
     * @return mixed
     *      [
     *          code => 1成功 2,失败
     *          mes => 成功: '' 失败: 错误信息
     *      ]
     */
    public static function sendPhone($appId, $templateId, $phone, $content, $key)
    {
        $request = Model::factoryCreate(__CLASS__);
        $data = [
            'appId' => $appId,
            'templateId' => $templateId,
            'phone' => $phone,
            'content' => is_array($content) ? Model::enCode($content, true) : $content,
            'ip' => getonlineip()
        ];
        $sign = RouteBase::createSign($data, $key);
        $data['sign'] = $sign['sign'];
        $url = ConfigPath::HOST_MESSAGE_CENTER . 'phone/index';
        $result = $request->getCurl()->curlPostJson($url, $data);
        $result2 = Model::deCode($result);
        return $result2 ? $result2 : ['code' => 2, 'mes' => '请求失败'];
    }

    public static function queryPhone($appId, $pkId, $key)
    {
        $request = Model::factoryCreate(__CLASS__);
        $data = [
            'appId' => $appId,
            'pkId' => $pkId,
            'ip' => getonlineip()
        ];
        $sign = RouteBase::createSign($data, $key);
        $data['sign'] = $sign['sign'];
        $url = ConfigPath::HOST_MESSAGE_CENTER . 'phone/query';
        $result = $request->getCurl()->curlGet($url . '?' . http_build_query($data));
        $result2 = Model::deCode($result);
        return $result2 ? $result2 : ['code' => 2, 'mes' => '请求失败'];
    }



//    /**
//     * 邮件群发接口
//     * @param $appId
//     * @param $templateId
//     * @param $toMails
//     * @param $content
//     * @param $key
//     * @return array|mixed
//     */
//    public static function sendEmaliMore($appId, $templateId, $toMails, $content, $key)
//    {
//        if (count($toMails) > 50) {
//            return [
//                'code' => 2, 'mes' => '一次最多只能给50个人发'
//            ];
//        }
//        $request = Model::factoryCreate('ProMessageCenter_Request');
//        $data = [
//            'appId' => $appId,
//            'templateId' => $templateId,
//            'toMail' => Model::enCode($toMails, true),
//            'content' => is_array($content) ? Model::enCode($content, true) : $content,
//            'ip' => getonlineip()
//        ];
//        $data['sign'] = RouteBase::createSign($data, $key);
//        $url = ConfigPath::HOST_MESSAGE_CENTER.'/mail/index';
//        $result = $request->getCurl()->curlGet($url, $data);
//        $result2 = Model::deCode($result);
//        return $result2 ? $result2 : ['code' => 2, 'mes' => '请求失败'];
//    }


    /**
     * 快递订阅
     * @param int $appId 应用id
     * @param $companyNum   快递公司编号
     * @param $waybill      快递单号
     * @param $from         出发城市
     * @param $to           目标城市
     * @param $key          key
     * @return array|mixed
     *      [
     *          code => 1成功 2,失败
     *          mes => 成功: '' 失败: '错误信息'
     *      ]
     */
    public static function expressSubcribe($appId, $companyNum, $waybill, $from, $to, $key)
    {
        $request = Model::factoryCreate(__CLASS__);
        $data = [
            'appId' => $appId,
            'companyNum' => $companyNum,
            'waybill' => $waybill,
            'from' => $from,
            'to' => $to,
            'ip' => getonlineip()
        ];
        $sign = RouteBase::createSign($data, $key);
        $data['sign'] = $sign['sign'];
        $url = ConfigPath::HOST_MESSAGE_CENTER . 'express/subscribe';
        $result = $request->getCurl()->curlGet($url . '?' . http_build_query($data));
        $result2 = Model::deCode($result);
        return $result2 ? $result2 : ['code' => 2, 'mes' => '请求失败'];
    }

    /**
     * 查询接口
     * @param $companyNum   快递公司编号
     * @param $waybill      快递单号
     * @param int $appId 应用id 可不填
     * @return array|mixed
     *      [
     *          code => 1成功 2,失败
     *          mes => 成功: [ 快递信息 ] 失败: 错误信息
     *      ]
     */
    public static function expressQuery($companyNum, $waybill, $appId = 0)
    {
        $request = Model::factoryCreate(__CLASS__);
        $data = [
            'appId' => $appId,
            'companyNum' => $companyNum,
            'waybill' => $waybill,
            'ip' => getonlineip()
        ];
        $url = ConfigPath::HOST_MESSAGE_CENTER . 'express/query';
        $result = $request->getCurl()->curlGet($url . '?' . http_build_query($data));
        $result2 = Model::deCode($result);
        return $result2 ? $result2 : ['code' => 2, 'mes' => '请求失败'];
    }


    /**
     * 回调接口 测试接口
     * @param $data
     * @return array|mixed
     */
    public static function expressNotify($data)
    {
        $request = Model::factoryCreate(__CLASS__);
        $url = ConfigPath::HOST_MESSAGE_CENTER . 'express/notify/kuaidi100';
        $result = $request->getCurl()->curlPost($url, $data);
        $result2 = Model::deCode($result);
        return $result2 ? $result2 : ['code' => 2, 'mes' => '请求失败'];
    }

    /**
     * @param $appId        appId
     * @param $productSn    商品sn
     * @param $productName  商品名字
     * @param $productDesc  商品描述
     * @param $amount       金额(单位分)
     * @param $otherListId  支付应用列表id
     * @param $key          密钥
     * @param $notifyUrl    回调地址
     * @param string $returnUrl 跳转地址
     * @param int $num 数量
     * @param int $timeOut 超时时间
     * @param array $ext 扩展字段,如微信公众号支付需要传递openId就放进去
     * @return array|mixed
     *      [
     *          code => 1成功 2,失败
     *          mes => 成功: [ sn 服务返回的支付订单, requsetData 提交数据 ] 失败: 错误信息
     *      ]
     */
    public static function pay($appId, $productSn, $productName, $productDesc, $amount, $otherListId, $key, $notifyUrl, $returnUrl = '', $ext = [], $num = 1, $timeOut = 30)
    {
        $data = [
            'appId' => $appId,
            'otherListId' => $otherListId,
            'amount' => $amount,
            'productName' => $productName,
            'productSn' => $productSn,
            'desc' => $productDesc,
            'timeOut' => $timeOut,
            'notifyUrl' => $notifyUrl,
            'returnUrl' => $returnUrl,
            'num' => $num,
            'ip' => getonlineip(),
            'ext' => Model::enCode($ext, true)
        ];
        $sign = RouteBase::createSign($data, $key);
        $data['sign'] = $sign['sign'];
        $request = Model::factoryCreate(__CLASS__);
        $url = ConfigPath::HOST_MESSAGE_CENTER . 'pay/index';
        $result = $request->getCurl()->curlGet($url . '?' . http_build_query($data));
        $result2 = Model::deCode($result);
        return $result2 ? $result2 : ['code' => 2, 'mes' => '请求失败'];
    }

    /**
     * 支付回调notify
     * @param $appId
     * @param $data 请使用 file_get_contents('php://input');
     * @param $otherListId
     * @param $key
     * @return array|mixed
     *      [
     *          code: 1成功, 2失败
     *          url: ''
     *          mes: 成功的订单号, 否则错误信息
     *       ]
     */
    public static function payNotify($appId, $data, $otherListId, $key)
    {
        $data = [
            'appId' => $appId,
            'otherListId' => $otherListId,
            'data' => $data,
            'ip' => getonlineip(),
        ];
        $sign = RouteBase::createSign($data, $key);
        $data['sign'] = $sign['sign'];
        $request = Model::factoryCreate(__CLASS__);
        $url = ConfigPath::HOST_MESSAGE_CENTER . 'pay/index';
        $result = $request->getCurl()->curlGet($url . '?' . http_build_query($data));
        $result2 = Model::deCode($result);
        return $result2 ? $result2 : ['code' => 2, 'mes' => '请求失败'];
    }

    /**
     * @param $code 1 成功 2 失败
     * @param $mes  返回错误信息, 在微信里面有用,其他无效
     * @param $type
     *      [
     *          ProMessageCenter_PayPay::ALIPAY_SDK_PAY,
     *          ProMessageCenter_PayPay::ALIPAY_WAP_PAY,
     *          ProMessageCenter_PayPay::ALIPAY_WEB_PAY,
     *          ProMessageCenter_PayPay::WEIXIN_PAY_APP,
     *          ProMessageCenter_PayPay::WEIXIN_PAY_JSDK,
     *          ProMessageCenter_PayPay::WEIXIN_PAY_WEB,
     *          ProMessageCenter_PayPay::HWY_SDK,
     *          ProMessageCenter_PayPay::HWY_WEB,
     *      ]
     * @return mixed
     *      返回要输出的信息
     * @throws Exception
     */
    public static function payNotifyMessage($code, $mes, $type = ProMessageCenter_PayPay::ALIPAY_SDK_PAY)
    {
        return ProMessageCenter_PayNotify::notifyMes($code == 1, $mes, $type);
    }


    /**
     * 关闭订单接口
     * @param $appId        appId
     * @param $sn           服务返回的支付订单号
     * @param $key
     * @param int $force 强制查询, 只适用后台, 前端千万别用
     * @return array|mixed
     *      [
     *          code => 1成功 2,失败
     *          mes => 成功: '成功' 失败: 错误信息
     *      ]
     */
    public static function payClose($appId, $sn, $key, $force = 0)
    {
        $data = [
            'appId' => $appId,
            'sn' => $sn,
            'force' => $force,
            'ip' => getonlineip()
        ];
        $sign = RouteBase::createSign($data, $key);
        $data['sign'] = $sign['sign'];
        $request = Model::factoryCreate(__CLASS__);
        $url = ConfigPath::HOST_MESSAGE_CENTER . 'pay/close';
        $result = $request->getCurl()->curlGet($url . '?' . http_build_query($data));
        $result2 = Model::deCode($result);
        return $result2 ? $result2 : ['code' => 2, 'mes' => '请求失败'];
    }

    /**
     * 查询订单接口
     * @param $appId    appId
     * @param $sn       服务返回的订单号
     * @param $key
     * @param int $force
     * @return array|mixed
     *      [
     *          code => 1成功 2,失败
     *          mes => 成功: '订单信息' 失败: 错误信息
     *      ]
     */
    public static function payQuery($appId, $sn, $key, $force = 0)
    {
        $data = [
            'appId' => $appId,
            'sn' => $sn,
            'force' => $force,
            'ip' => getonlineip()
        ];
        $sign = RouteBase::createSign($data, $key);
        $data['sign'] = $sign['sign'];
        $request = Model::factoryCreate(__CLASS__);
        $url = ConfigPath::HOST_MESSAGE_CENTER . 'pay/query';
        $result = $request->getCurl()->curlGet($url . '?' . http_build_query($data));
        $result2 = Model::deCode($result);
        return $result2 ? $result2 : ['code' => 2, 'mes' => '请求失败'];
    }

    /**
     * 退款接口
     * @param $appId            appId
     * @param $sn               服务返回的订单号
     * @param $refundAmount     退款金额
     * @param $refundDesc       退款描述
     * @param $key
     * @param int $force
     * @return array|mixed
     *      [
     *          code => 1成功 2,失败
     *          mes => 成功: '退款唯一订单号' 失败: 错误信息
     *      ]
     */
    public static function payRefound($appId, $sn, $refundAmount, $refundDesc, $key, $force = 0)
    {
        $data = [
            'appId' => $appId,
            'refundAmount' => $refundAmount,
            'refundDesc' => $refundDesc,
            'sn' => $sn,
            'force' => $force,
            'ip' => getonlineip()
        ];
        $sign = RouteBase::createSign($data, $key);
        $data['sign'] = $sign['sign'];
        $request = Model::factoryCreate(__CLASS__);
        $url = ConfigPath::HOST_MESSAGE_CENTER . 'pay/refound';
        $result = $request->getCurl()->curlGet($url . '?' . http_build_query($data));
        $result2 = Model::deCode($result);
        return $result2 ? $result2 : ['code' => 2, 'mes' => '请求失败'];
    }

    /**
     * 退款查询接口
     * @param $appId        appId
     * @param $sn           服务返回的退款订单号
     * @param $key
     * @param int $force
     * @return array|mixed
     *      [
     *          code => 1成功 2,失败
     *          mes => 成功: [ status=> 退款状态, mes=> '状态描述' ] 失败: 错误信息
     *      ]
     */
    public static function payRefoundQuery($appId, $sn, $key, $force = 0)
    {
        $data = [
            'appId' => $appId,
            'refundSn' => $sn,
            'force' => $force,
            'ip' => getonlineip()
        ];
        $sign = RouteBase::createSign($data, $key);
        $data['sign'] = $sign['sign'];
        $request = Model::factoryCreate(__CLASS__);
        $url = ConfigPath::HOST_MESSAGE_CENTER . 'pay/refoundQuery';
        $result = $request->getCurl()->curlGet($url . '?' . http_build_query($data));
        $result2 = Model::deCode($result);
        return $result2 ? $result2 : ['code' => 2, 'mes' => '请求失败'];
    }


    /**
     * @param $appId
     * @param $key
     * @return array|mixed
     * @throws Exception
     *      [
     *              code => 1,
     *              mes => $access_token
     *      ]
     */
    public static function wxAccessToken($appId, $key)
    {
        $data = [
            'appId' => $appId,
            'ip' => getonlineip()
        ];
        $sign = RouteBase::createSign($data, $key);
        $data['sign'] = $sign['sign'];
        $request = Model::factoryCreate(__CLASS__);
        $url = ConfigPath::HOST_MESSAGE_CENTER . 'wx/accessToken';
        $result = $request->getCurl()->curlGet($url . '?' . http_build_query($data));
        $result2 = Model::deCode($result);
        return $result2 ? $result2 : ['code' => 2, 'mes' => '请求失败'];
    }


    /**
     * @param $appId
     * @param $key
     * @param $notifyUrl
     * @return array|mixed
     * @throws Exception
     *      [
     *          code => 1,
     *          mes =>  {"appId":"wxc5f2bd0b668dd77a",
     *                   "nonceStr":"VsrxHifeHlQJT8pl",
     *                  "timestamp":1499694376,
     *                  "url":"http:\/\/tianshengwocha.com",
     *                  "signature":"934cfa311163432cb61a6d8e3a240a1a7edf65b0"},,
     *      ]
     */
    public static function wxJsdk($appId, $key, $notifyUrl)
    {
        $data = [
            'appId' => $appId,
            'notifyUrl' => $notifyUrl,
            'ip' => getonlineip()
        ];
        $sign = RouteBase::createSign($data, $key);
        $data['sign'] = $sign['sign'];
        $request = Model::factoryCreate(__CLASS__);
        $url = ConfigPath::HOST_MESSAGE_CENTER . 'wx/jsdk';
        $result = $request->getCurl()->curlGet($url . '?' . http_build_query($data));
        $result2 = Model::deCode($result);
        return $result2 ? $result2 : ['code' => 2, 'mes' => '请求失败'];
    }

    /**
     * @param $appId
     * @param $key
     * @param $menus
     * @return array|mixed
     * @throws Exception
     *      [
     *          code => 1,
     *          mes => [
     *
     *          ],
     *      ]
     */
    public static function wxMenuSave($appId, $key, $menus)
    {
        $data = [
            'appId' => $appId,
            'menu' => $menus,
            'ip' => getonlineip()
        ];
        $sign = RouteBase::createSign($data, $key);
        $data['sign'] = $sign['sign'];
        $request = Model::factoryCreate(__CLASS__);
        $url = ConfigPath::HOST_MESSAGE_CENTER . 'wx/menu/save';
        $result = $request->getCurl()->curlGet($url . '?' . http_build_query($data));
        $result2 = Model::deCode($result);
        return $result2 ? $result2 : ['code' => 2, 'mes' => '请求失败'];
    }

    /**
     * @param $appId
     * @param $key
     * @return array|mixed
     * @throws Exception
     *      [
     *            code => 1,
     *            mes => ok,
     *      ]
     */
    public static function wxMenuDel($appId, $key)
    {
        $data = [
            'appId' => $appId,
            'ip' => getonlineip()
        ];
        $sign = RouteBase::createSign($data, $key);
        $data['sign'] = $sign['sign'];
        $request = Model::factoryCreate(__CLASS__);
        $url = ConfigPath::HOST_MESSAGE_CENTER . 'wx/menu/del';
        $result = $request->getCurl()->curlGet($url . '?' . http_build_query($data));
        $result2 = Model::deCode($result);
        return $result2 ? $result2 : ['code' => 2, 'mes' => '请求失败'];
    }

    /**
     * @param $appId
     * @param $key
     * @return array|mixed
     * @throws Exception
     *      [
     *          code => 1,
     *          mes => {"menu":{"button":[
     *          {"type":"click","name":"今日歌曲","key":"V1001_TODAY_MUSIC","sub_button":[]},
     *          {"type":"click","name":"歌手简介","key":"V1001_TODAY_SINGER","sub_button":[]},
     *          {"name":"菜单","sub_button":[{"type":"view","name":"搜索","url":"http://www.soso.com/","sub_button":[]},
     *          {"type":"view","name":"视频","url":"http://v.qq.com/","sub_button":[]},
     *          {"type":"click","name":"赞一下我们","key":"V1001_GOOD","sub_button":[]}]}]}},
     *      ]
     */
    public static function wxMenuList($appId, $key)
    {
        $data = [
            'appId' => $appId,
            'ip' => getonlineip()
        ];
        $sign = RouteBase::createSign($data, $key);
        $data['sign'] = $sign['sign'];
        $request = Model::factoryCreate(__CLASS__);
        $url = ConfigPath::HOST_MESSAGE_CENTER . 'wx/menu/list';
        $result = $request->getCurl()->curlGet($url . '?' . http_build_query($data));
        $result2 = Model::deCode($result);
        return $result2 ? $result2 : ['code' => 2, 'mes' => '请求失败'];
    }


    /**
     * 邮箱验证码
     * @param $appId
     * @param $key
     * @param $toMail
     * @param $templateId
     * @return array|mixed
     * @throws Exception
     */
    public static function yzmMail($appId, $key, $toMail, $templateId)
    {
        $data = [
            'appId' => $appId,
            'toMail' => $toMail,
            'templateId' => $templateId,
            'ip' => getonlineip()
        ];
        $sign = RouteBase::createSign($data, $key);
        $data['sign'] = $sign['sign'];
        $request = Model::factoryCreate(__CLASS__);
        $url = ConfigPath::HOST_MESSAGE_CENTER . 'yzm/mail';
        $result = $request->getCurl()->curlGet($url . '?' . http_build_query($data));
        $result2 = Model::deCode($result);
        return $result2 ? $result2 : ['code' => 2, 'mes' => '请求失败'];
    }

    /**
     * 图片验证码
     * @param $appId
     * @param $key
     * @return array|mixed
     * @throws Exception
     */
    public static function yzmPic($appId, $key)
    {
        $data = [
            'appId' => $appId,
            'ip' => getonlineip()
        ];
        $sign = RouteBase::createSign($data, $key);
        $data['sign'] = $sign['sign'];
        $request = Model::factoryCreate(__CLASS__);
        $url = ConfigPath::HOST_MESSAGE_CENTER . 'yzm';
        $result = $request->getCurl()->curlGet($url . '?' . http_build_query($data));
        $result2 = Model::deCode($result);
        return $result2 ? $result2 : ['code' => 2, 'mes' => '请求失败'];
    }

    /**
     * 手机验证码
     * @param $appId
     * @param $key
     * @param $phone
     * @param $templateId
     * @return array|mixed
     * @throws Exception
     */
    public static function yzmPhone($appId, $key, $phone, $templateId)
    {
        $data = [
            'appId' => $appId,
            'phone' => $phone,
            'templateId' => $templateId,
            'ip' => getonlineip()
        ];
        $sign = RouteBase::createSign($data, $key);
        $data['sign'] = $sign['sign'];
        $request = Model::factoryCreate(__CLASS__);
        $url = ConfigPath::HOST_MESSAGE_CENTER . 'yzm/phone' . '?' . http_build_query($data);
        $result = $request->getCurl()->curlGet($url);
        $result2 = Model::deCode($result);
        return $result2 ? $result2 : ['code' => 2, 'mes' => '请求失败'];
    }

    /**
     * 验证码校验
     * @param $appId
     * @param $key
     * @param $pkId
     * @param $code
     * @param $type
     * @return array|mixed
     * @throws Exception
     */
    public static function yzmCheck($appId, $key, $pkId, $code, $type = ProMessageCenter_Code::KEY_TYPE_PHONE)
    {
        $data = [
            'appId' => $appId,
            'pkId' => $pkId,
            'code' => $code,
            'type' => $type,
            'ip' => getonlineip()
        ];
        $sign = RouteBase::createSign($data, $key);
        $data['sign'] = $sign['sign'];
        $request = Model::factoryCreate(__CLASS__);
        $url = ConfigPath::HOST_MESSAGE_CENTER . 'yzm/check';
        $result = $request->getCurl()->curlGet($url . '?' . http_build_query($data));
        $result2 = Model::deCode($result);
        return $result2 ? $result2 : ['code' => 2, 'mes' => '请求失败'];
    }


    /**
     * 微信模版消息, 多人
     * @param $appId
     * @param $key
     * @param $openId 可字符串, 也可以数组
     * @param $templateId
     * @param $content
     *        [
     *              'first' => '',
     *               '关键字1' => '',
     *               '关键字2' => '',
     *               '关键字3' => '',
     *              'remark' => ''
     *          ]
     * @param $ext
     *          [
     *              'url' => '',
     *              'appid' => '',
     *              'pagepath' => '',
     *          ]
     * @return array|mixed
     * @throws Exception
     */
    public static function wxTemplateOne($appId, $key, $openId, $templateId, $content, $ext)
    {
        $data = [
            'appId' => $appId,
            'openId' => is_array($openId) ? implode('|', $openId) : $openId,
            'templateId' => $templateId,
            'content' => Model::enCode($content, true),
            'ext' => Model::enCode($ext, true),
            'ip' => getonlineip()
        ];
        $sign = RouteBase::createSign($data, $key);
        $data['sign'] = $sign['sign'];
        $request = Model::factoryCreate(__CLASS__);
        $url = ConfigPath::HOST_MESSAGE_CENTER . 'wx/message/one';
        $result = $request->getCurl()->curlPostJson($url, $data);
//        $result = $request->getCurl()->curlGet($url . '?' . http_build_query($data));
        log_message('WEIXIN INFO',$result,'notice');

        $result2 = Model::deCode($result);
        return $result2 ? $result2 : ['code' => 2, 'mes' => '请求失败'];
    }

    /**
     * 微信模版消息群发所有人
     * @param $appId
     * @param $key
     * @param $content 字符串, 一串文本内容
     * @return array|mixed
     * @throws Exception
     */
    public static function wxTemplateAll($appId, $key, $content)
    {
        $data = [
            'appId' => $appId,
            'content' => Model::enCode($content, true),
            'ip' => getonlineip()
        ];
        $sign = RouteBase::createSign($data, $key);
        $data['sign'] = $sign['sign'];
        $request = Model::factoryCreate(__CLASS__);
        $url = ConfigPath::HOST_MESSAGE_CENTER . 'wx/message/all';
        $result = $request->getCurl()->curlPostJson($url, $data);
//        $result = $request->getCurl()->curlGet($url . '?' . http_build_query($data));
        $result2 = Model::deCode($result);
        return $result2 ? $result2 : ['code' => 2, 'mes' => '请求失败'];
    }

    /**
     * 推送消息 多人
     * @param $appId
     * @param $key
     * @param $uid  可字符串, 也可以数组
     * @param $templateId
     * @param $content
     *          [
     *              关键字1: 1
     *              关键字2: 2
     *           ]
     * @return array|mixed
     * @throws Exception
     */
    public static function pushAlertOne($appId, $key, $uid, $templateId, $content)
    {
        $data = [
            'appId' => $appId,
            'target' => is_array($uid) ? implode('|', $uid) : $uid,
            'templateId' => $templateId,
            'content' => is_array($content) ? Model::enCode($content, true) : $content,
            'ip' => getonlineip()
        ];
        $sign = RouteBase::createSign($data, $key);
        $data['sign'] = $sign['sign'];
        $request = Model::factoryCreate(__CLASS__);
        $url = ConfigPath::HOST_MESSAGE_CENTER . 'push/alert/one';
        $result = $request->getCurl()->curlPostJson($url, $data);
        $result2 = Model::deCode($result);
        return $result2 ? $result2 : ['code' => 2, 'mes' => '请求失败'];
    }

    /**
     * 推送消息 所有人
     * @param $appId
     * @param $key
     * @param $templateId
     * @param $content
     *          [
     *              关键字1: 1
     *              关键字2: 2
     *           ]
     * @return array|mixed
     * @throws Exception
     */
    public static function pushAlertAll($appId, $key, $templateId, $content)
    {
        $data = [
            'appId' => $appId,
            'templateId' => $templateId,
            'content' => is_array($content) ? Model::enCode($content, true) : $content,
            'ip' => getonlineip()
        ];
        $sign = RouteBase::createSign($data, $key);
        $data['sign'] = $sign['sign'];
        $request = Model::factoryCreate(__CLASS__);
        $url = ConfigPath::HOST_MESSAGE_CENTER . 'push/alert/all';
        $result = $request->getCurl()->curlPostJson($url, $data);
        $result2 = Model::deCode($result);
        return $result2 ? $result2 : ['code' => 2, 'mes' => '请求失败'];
    }


}