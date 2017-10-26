<?php
if (!defined('IN_BOOT')) {
    exit('Access Denied');
}
/**
 *  $tomail                = 'wanxiaokuo@qq.comn';//发送邮件地址
 * $tilte                = '来自'.$titleName.'的注册确认信';//标题--这里原来为会长的账号，现在改为推广后台的前端展示名称
 * $fromName            = $titleName;//发件人
 * $content            = '你好：感谢使用我们的服务！<br />22222已为您进行了身份认证。<br />
 * 请<a href="'.$registerUrl.'">点击此处</a>激活。<br />如果不能点击，您也可以尝试使用下方的地址来激活:<br />'.$registerUrl.'<br />
 * 激活后，您可以通过'.$clerkDomain.'来登录管理后台，进行更多的操作。<br>' .
 * '用户名：'.$tomail.'<br />'.
 * '登录密码：'.$password.'<br />'.
 * '请妥善保管您的用户名和密码。';
 *
 *
 * include ROOT.'/lib/function/send_mail.php';
 * send_mail($tomail, $content, $tilte, $fromName);
 */

include_once ROOT . '/config/config.email.php';
function send_mail($tomail, $content, $tilte = '')
{
    if (empty($tomail) || empty($content)) return false;
    //声明类
    $mail = new PhpMailer();
    // 设置使用 SMTP
    $mail->IsSMTP();
    // 指定的 SMTP 服务器地址
    $mail->Host = "smtp.exmail.qq.com";
    // 设置为安全验证方式
    $mail->SMTPAuth = true;
    // SMTP 发邮件人的用户名
    $mail->Username = Config_QQ_Email::getUserName();
    // SMTP 密码
    $mail->Password = Config_QQ_Email::getPassWord();
    $from = Config_QQ_Email::getFrom();
    $fromName = Config_QQ_Email::getFromName();
    $mail->From = $from;
    $mail->FromName = "=?utf-8?B?" . base64_encode($fromName . "<" . $from . ">") . "?=";
    $mail->AddAddress($tomail);
    $mail->AddReplyTo($from, $fromName);
    $mail->WordWrap = 50;
    $mail->IsHTML(true);
    $mail->CharSet = "utf-8";
    $mail->Encoding = "base64";
    $mail->Subject = "=?utf-8?B?" . base64_encode($tilte) . "?=";
    $mail->Body = $content;
    $result = $mail->Send();

    if (!$result) {
        return false;
    } else {
        return true;
    }
}