<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/8
 * Time: 14:52
 */
class Wx_Menu extends ProMessageCenter_MessageCheck
{
    public function __construct()
    {
        parent::__construct();
        param_request(array(
            'appId' => 'INT',
            'ip' => 'STRING',
            'menu' => 'STRING',
            'sign' => 'STRING'
        ), '', $this->param, array(
            'appId' => 0,
            'ip' => '',
            'menu' => '',
            'sign' => ''
        ));
    }


    public function saveFunc()
    {
        $_POST = $this->param;
        $rules = array(
            'appId|应用ID' => 'required|trim|callback_checkApp',
            'ip|ip' => 'trim',
            'menu|menu' => 'required|trim',
            'sign' => "required|trim|exact_length[32]|callback_checkSign"
        );
        $this->form->set_rules($rules);
        if ($this->form->run() === false) {
            show_message($this->form->error_string(), '', 2);
        }
        $info = $this->form->get_validation_data();
        $appId = $info['appId'];
        $menu = Model::deCode($info['menu']);
        $code = 1;
        $mes = 'ok';
        try {
            $config = WxDrive_Config::createConfigByAppId($appId);
            $proAccessToken = new ProMessageCenter_WxAccessToken($config);
            $proMenu = new ProMessageCenter_WxMenu();
            $proMenu->save($menu, $proAccessToken->getAccessToken($appId));
        } catch (Exception $e) {
            $code = $e->getCode();
            $mes = $e->getMessage();
        }
        show_message($mes, '', $code);
    }

    public function listFunc()
    {
        $_POST = $this->param;
        $rules = array(
            'appId|应用ID' => 'required|trim|callback_checkApp',
            'ip|ip' => 'trim',
            'sign' => "required|trim|exact_length[32]|callback_checkSign"
        );
        $this->form->set_rules($rules);
        if ($this->form->run() === false) {
            show_message($this->form->error_string(), '', 2);
        }
        $info = $this->form->get_validation_data();
        $appId = $info['appId'];

        try {
            $config = WxDrive_Config::createConfigByAppId($appId);
            $proAccessToken = new ProMessageCenter_WxAccessToken($config);
            $proMenu = new ProMessageCenter_WxMenu();
            $mes = $proMenu->listMenu($proAccessToken->getAccessToken($appId));
        } catch (Exception $e) {
            $code = $e->getCode();
            $mes = $e->getMessage();
        }
        show_message($mes, '', $code);
    }


    public function del()
    {
        $_POST = $this->param;
        $rules = array(
            'appId|应用ID' => 'required|trim|callback_checkApp',
            'ip|ip' => 'trim',
            'sign' => "required|trim|exact_length[32]|callback_checkSign"
        );
        $this->form->set_rules($rules);
        if ($this->form->run() === false) {
            show_message($this->form->error_string(), '', 2);
        }
        $info = $this->form->get_validation_data();
        $appId = $info['appId'];
        $mes = 'ok';
        try {
            $config = WxDrive_Config::createConfigByAppId($appId);
            $proAccessToken = new ProMessageCenter_WxAccessToken($config);
            $proMenu = new ProMessageCenter_WxMenu($config);
            $proMenu->del($proAccessToken->getAccessToken($appId));
        } catch (Exception $e) {
            $code = $e->getCode();
            $mes = $e->getMessage();
        }
        show_message($mes, '', $code);
    }


}