<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/31
 * Time: 11:08
 */
class Kuaidi_KuaiDi100
{
    const HOST = 'http://www.kuaidi100.com';
//    const APP_Key = 'nNbfuyEs3223';

    private $notifyUrl = '';
    private $appKey = '';
    private $salt = '';

    public function __construct($config)
    {
        $this->notifyUrl = $config['notifyUrl'];
        $this->appKey = $config['appKey'];
        $this->salt = $config['salt'];
    }


    /**
     * 0：在途，即货物处于运输过程中；
     * 1：揽件，货物已由快递公司揽收并且产生了第一条跟踪信息；
     * 2：疑难，货物寄送过程出了问题；
     * 3：签收，收件人已签收；
     * 4：退签，即货物由于用户拒签、超区等原因退回，而且发件人已经签收；
     * 5：派件，即快递正在进行同城派件；
     * 6：退回，货物正处于退回发件人的途中；
     * @var array
     */
    private $_status = array(
        0 => Kuaidi_Data::STATUS_JOURNEY,
        1 => Kuaidi_Data::STATUS_TOOK,
        2 => Kuaidi_Data::STATUS_DIFFICULTY,
        3 => Kuaidi_Data::STATUS_SIGN,
        4 => Kuaidi_Data::STATUS_REFUSAL,
        5 => Kuaidi_Data::STATUS_SEND,
        6 => Kuaidi_Data::STATUS_RETURN
    );

    protected function getOfCurl($no, $num)
    {
//        $url ='http://api.kuaidi100.com/api?id='.$AppKey.'&com='.$typeCom.'&nu='.$typeNu.'&show=2&muti=1&order=asc';
        $host = self::HOST;
        $path = "/api";
        $querys = array(
            'id' => $this->appKey,
            'com' => self::getNo($no),
            'nu' => $num,
            'show' => 0, //0：返回json字符串，
            'muti' => 1, //1:返回多行完整的信息
            'order' => 'asc',
        );
        $querys = http_build_query($querys);

        $url = $host . $path . "?" . $querys;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.57');
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);
        $str = curl_exec($curl);
        curl_close($curl);
        $data = @json_encode($str, 1);
        if (empty($data) || is_string($data)) {
            $data = array();
            log_message('error', '接口异常[url: ' . $url . '|return:' . $str . ']', 'kuaidi');
        } else {
            /**
             * 返回的数据需要处理，接口未完善
             */
            if ($data['status'] = 1) {
                $data = array(
                    'status' => $this->getStatusWithPrivate($data['state']),
                    'data' => $this->getDataWithPrivate($data['data'])
                );
            } elseif ($data['status'] == 2) {
                $data = array();
                //接口异常
                log_message('error', '接口异常[url: ' . $url . '|return:' . $str . ']', 'kuaidi');
            }
        }
        return $data;
    }

    protected function getStatusWithPrivate($status)
    {
        return $this->_status[$status];
    }


    private function getDataWithPrivate($data)
    {
        $result = array();
        if (empty($data[0]) || is_array($data[0])) {
            $result = $data;
        } else {
            foreach ($data as $v) {
                log_message('EXPRESS INFO',$v['context'],'salt');

                $result[] = array(
                    'context' => $v['context'],
                    'time' => $v['ftime']
                );
            }
        }
        return $result;
    }


    /**
     * @param array $info
     *      no      快递公司编码
     *      number  快递单号
     *      from    出发地城市
     *      to      目的地城市
     * @return array
     *      code 1成功 2失败
     *      mes 错误信息
     */
    public function subscribe($info = array())
    {
        global $_SC;
        $url = self::HOST . '/poll';
        $param = array(
            //订阅的快递公司的编码，一律用小写字母，见章五《快递公司编码》
            'company' => $info['companyNum'],
            //订阅的快递单号，单号的最大长度是32个字符
            'number' => $info['waybill'],
            //出发地城市
            'from' => $info['from'],
            //目的地城市，到达目的地后会加大监控频率
            'to' => $info['to'],
            //授权码，签订合同后发放
            'key' => $this->appKey,
            'parameters' => array(
                //回调地址
                'callbackurl' => $this->notifyUrl,
                'salt' => $this->salt
            )
        );

        $post_data = array(
            'schema' => 'json',
            'param' => json_encode($param, JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES )
        );

        $o = "";
        foreach ($post_data as $k => $v) {
            $o .= "$k=" . $v . "&";        //默认UTF-8编码格式
        }

        $post_data = substr($o, 0, -1);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        log_message('kuaidi100', $result.'  '.$post_data, 'kuaidi100');
        curl_close($ch);
        $data = json_decode($result, 1);
        $code = 1;
        $msg = $result;

        if (empty($data) || !($data['result'] == 'true' && $data['returnCode'] == '200' && $data['message'] == '提交成功')) {
            $code = 2;
//            $msg = $result;
        }
        return array(
            'code' => $code,
            'mes' => $msg
        );
    }


    /**
     * 订阅回调接口
     * @return mixed
     *          companyNum      本地快递编码
     *          waybill  快递单号
     *          status  本地快递状态标识
     *          data    详细信息
     *          restart 是否需要重新订阅\
     *          message message
     */
    public function notify($param)
    {

//        $str = '{"status":"polling","billstatus":"got","message":"","lastResult":{"message":"ok","state":"0","status":"200","condition":"F00","ischeck":"0","com":"shunfeng","nu":"V030344422","data":[{"context":"\u4e0a\u6d77\u5206\u62e8\u4e2d\u5fc3\/\u88c5\u4ef6\u5165\u8f66\u626b\u63cf ","time":"2012-08-28 16:33:19","ftime":"2012-08-28 16:33:19","status":"\u5728\u9014","areaCode":"310000000000","areaName":"\u4e0a\u6d77\u5e02"},{"context":"\u4e0a\u6d77\u5206\u62e8\u4e2d\u5fc3\/\u4e0b\u8f66\u626b\u63cf ","time":"2012-08-27 23:22:42","ftime":"2012-08-27 23:22:42","status":"\u5728\u9014","areaCode":"310000000000","areaName":"\u4e0a\u6d77\u5e02"}]}}';
        $param = $param['param'];
        $param = json_decode($param, 1);
        /*数据转换*/
        $data = array(
            // 公司编号
            'companyNum' => $param["lastResult"]['com'],
            // 快递编码
            'waybill' => $param["lastResult"]['nu'],
            //具体状态
            'status' => $this->getStatusWithPrivate($param["lastResult"]['state']),
            // 数据
            'data' => $this->getDataWithPrivate($param ["lastResult"]['data']),
            // 是否需要重新提交
            'restart' => $param['status'] == 'abort' && mb_strpos($param ["message"], '3天') !== false,
            // message
            'message' => $param['status'] . '|' . $param ["message"]
        );

        return $data;


    }

    /**
     * @param $code
     * @param $mes string
     * @return array
     */
    public function response($code, $mes)
    {
        $result = false;
        if ($code == Kuaidi_Exception::STATUS_SUCCESS) {
            $result = true;
        }
        return array(
            "result" => $result,
            "returnCode" => $code,
            "message" => $mes
        );
    }

    /**
     * 是否重新发送
     * @param $data
     * @return bool
     */
    public function checkRestart($data)
    {
        return $data['subscribeCount'] < 4;
    }

    public function checkNotifySign($params, $config)
    {
        log_message('EXPRESS INFO',$params['sign'],'salt');
        log_message('EXPRESS INFO',md5($params['param'] . $config['salt']),'salt');
        log_message('EXPRESS INFO',$params['param'],'salt');
        log_message('EXPRESS INFO',$config['salt'],'salt');

        return strtolower($params['sign']) == md5($params['param'] . $config['salt']);
    }
}