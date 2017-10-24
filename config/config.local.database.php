<?php
if (!defined('IN_BOOT')) {
    exit('Access Denied');
}

$_SC['messageCenter'] = array(
    'db' => array(
        'host' => '192.168.1.249',
        'username' => 'root',
        'password' => '',
        'dbname' => '',
    ),
    'sdb' => array(
        array(
            'host' => '192.168.1.249',
            'username' => 'root',
            'password' => '',
            'dbname' => '',
        )
    )
);


/*ftp*/
$_SC['ftp_host'] = '192.168.1.249';    //ftp地址
$_SC['ftp_user'] = 'root';            //ftp帐号
$_SC['ftp_pass'] = '';    //ftp密码
$_SC['ftp_port'] = '21';                //ftp端口


/*redis*/
$_SC['redis_cachehost'] = '192.168.1.249';
$_SC['redis_cacheport'] = 6379;
$_SC['redis_passport'] = '';



