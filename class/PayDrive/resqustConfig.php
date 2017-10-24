<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/8
 * Time: 10:27
 */
class PayDrive_resqustConfig
{
    private $id;
    private $name;
    private $title;
    private $partner;
    private $partnerName;
    private $partnerKey;
    private $partnerSecret;
    private $partnerAppId;
    private $notifyUrl;
    private $returnUrl;
    private $publicRSA2;
    private $privateRSA2;
    private $driveType;

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
            throw new Exception(500, 'PayDrive_resqustData类没有该方法:' . $name);
        }
    }

//    public function createOrderSn($split_char = 'S')
//    {
//        'S'
//    }


}