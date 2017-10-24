<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/8
 * Time: 12:43
 */
class Wx_AccessToken extends ProMessageCenter_MessageCheck
{
    public function __construct()
    {
        parent::__construct();
        param_request(array(
            'appId' => 'INT',
            'ip' => 'STRING',
            'sign' => 'STRING'
        ), '', $this->param, array(
            'appId' => 0,
            'ip' => '',
            'sign' => ''
        ));
    }

    public function testFunc(){
        $file = fopen("test.txt","w+");
        if (flock($file,LOCK_EX))
        {
            echo "Error locking111111 file!";
            // fwrite($file,"Write something");
            // release lock
            //flock($file,LOCK_UN);
        }
        else
        {
            echo "Error locking file!";
        }
        sleep(20);
        flock($file,LOCK_UN);
        fclose($file);

        echo 1111;
    }

    public function test2Func(){
        $file = fopen("test.txt","w+");
//        if (flock($file,LOCK_EX))
//        {
//            echo "Error 222222 file!";
//            // fwrite($file,"Write something");
//            // release lock
//            //flock($file,LOCK_UN);
//        }
//        else
//        {
//            echo "Error 3333333 file!";
//        }
//        flock($file,LOCK_UN);
        fclose($file);
        echo 55;
    }

    public function indexFunc()
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
        $code = 1;
        try {
            $config = WxDrive_Config::createConfigByAppId($appId);
            $proAccessToken = new ProMessageCenter_WxAccessToken($config);
            $mes = $proAccessToken->getAccessToken($appId);
        } catch (Exception $e) {
            $code = $e->getCode();
            $mes = $e->getMessage();
        }
        show_message($mes, '', $code);
    }
}