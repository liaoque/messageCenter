<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/8
 * Time: 10:27
 */
class PayDrive_OrderData
{
    //订单id
    private $id;
    private $sn;
    //app表的id, 对接该服务的应用id
    private $appId;
    private $amount;
    private $ip;
    private $createTime;
    private $status;
    //三方接口id
    private $otherListId;
    private $productName;
    private $productSn;
    private $notifyUrl;
    private $returnUrl;
    private $timeOut = 30;
    private $desc;
    private $num;
    private $refundSn;
    private $refundAmount;
    private $refundDesc;
    private $refundCreateTime;


    public function __construct($info)
    {
        foreach ($info as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        $str = substr($name, 0, 3);
        if ($str == 'get') {
            $pro = lcfirst(substr($name, 3));
            return empty($this->$pro) ? '' : $this->$pro;
        } elseif ($str == 'set') {
            $pro = lcfirst(substr($name, 3));
            return $this->$pro = empty($arguments[0]) ? '' : $arguments[0];
        } else {
            throw new Exception('PayDrive_resqustConfig 类没有该方法:' . $name, PayDrive_PayException::ERROR_SYS);
        }
    }

    public function getCreateTime($fomart = 'Y-m-d H:i:s')
    {
        return empty($this->createTime) ? ($this->createTime = date($fomart)) : date($fomart, strtotime($this->createTime));
    }

    public function getNum()
    {
        return empty($this->num) ? 1 : $this->num;
    }

    public function getProductName($productName = '')
    {
        return empty($this->productName) ? $productName : $this->productName;
    }

    public function getRefundCreateTime($fomart = 'Y-m-d H:i:s')
    {
        return empty($this->refundCreateTime) ? date($fomart) : date($fomart, strtotime($this->refundCreateTime));
    }

    public function createOrderSn($id = null, $split_char = 'S')
    {
        if (!$id) {
            $id = mt_rand(1000000, 9999999);
        }
        $data = [
            $this->appId,
            $this->otherListId,
            $id,
            $this->getCreateTime('YmdHis'),
            mt_rand(1, 100)
        ];
        return $split_char.implode($split_char, $data);
    }

    public function createRefundSn($split_char = 'S')
    {
        $refundCreateTime = $this->getRefundCreateTime('YmdHis');
        return $this->sn . $split_char . $refundCreateTime;
    }

    public function __isset($name)
    {
        // TODO: Implement __isset() method.
        if (!$this->$name) {
            throw new Exception($name . '不能为空', PayDrive_PayException::ERROR_PRO_EMPTY);
        }
    }

}