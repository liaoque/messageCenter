<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/16
 * Time: 11:33
 */
class PayDrive_PayBase
{
    protected $config;

    public function __construct(PayDrive_resqustConfig $resqustData)
    {
        $this->config = $resqustData;
    }
}