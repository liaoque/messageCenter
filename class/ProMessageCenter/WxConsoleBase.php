<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/8
 * Time: 13:07
 */
class ProMessageCenter_WxConsoleBase
{
    private $config;

    public function __construct(WxDrive_Config $config = null)
    {
        if ($config) {
            $this->setConfig($config);
        }
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param mixed $config
     */
    public function setConfig(WxDrive_Config $config)
    {
        $this->config = $config;
    }
    

}