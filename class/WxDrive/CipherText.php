<?php

/**
 * 密文
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/3
 * Time: 11:19
 * 在安全模式或兼容模式下，url上会新增两个参数encrypt_type和msg_signature。encrypt_type表示加密类型，msg_signature:表示对消息体的签名。
 * url上无encrypt_type参数或者其值为raw时表示为不加密；encrypt_type为aes时，表示aes加密（暂时只有raw和aes两种值)。公众帐号开发者根据此参数来判断微信公众平台发送的消息是否加密。
 * 兼容模式和安全模式加解密的方法完全一样，兼容模式的xml消息体比安全模式多了几个明文字段，具体请查看《消息加解密详细技术方案》。
 */
class WxDrive_CipherText extends WxDrive_Console
{

    private $wxBizMsgCrypt = null;


    public function __construct()
    {
        parent::__construct();
        if (empty($this->wXBizMsgCrypt)) {
            include_once ROOT . "/lib/wxBizMsgCrypt/wxBizMsgCrypt.php";
            $token = $this->getConfig()->getToken();
            $encodingAesKey = $this->getConfig()->getEncodingAesKey();
            $appId = $this->getConfig()->getAppId();
            // @param $token: 公众平台上，开发者设置的Token
            // @param $encodingAesKey: 公众平台上，开发者设置的EncodingAESKey
            // @param $appId: 公众号的appid
            $this->wxBizMsgCrypt = new wxBizMsgCrypt($token, $encodingAesKey, $appId);
        }
    }

    public function getWxBizMsgCrypt()
    {
        return $this->wxBizMsgCrypt;
    }

    public function enCode($text, $timeStamp)
    {
        $string = '';
        $nonce = '';
        // @param $text:公众号待回复用户的消息，xml格式的字符串
        // @param $timeStamp: 时间戳，可以自己生成，也可以用URL参数的timestamp
        // @param $nonce: 随机串，可以自己生成，也可以用URL参数的nonce
        // @param $string: 加密后的可以直接回复用户的密文，包括msg_signature, timestamp, nonce, encrypt的xml格式的字符串,当return返回0时有效
        // return：成功0，失败返回对应的错误码
        $error = $this->getWxBizMsgCrypt()->encryptMsg($text, $timeStamp, $nonce, $string);
        return $error ? false : $string;
    }

    public function deCode($encryptMsg, $timeStamp)
    {
        $nonce = '';
        $xml_tree = new DOMDocument();
        $xml_tree->loadXML($encryptMsg);
        $array_e = $xml_tree->getElementsByTagName('Encrypt');
        $array_s = $xml_tree->getElementsByTagName('MsgSignature');
        $encrypt = $array_e->item(0)->nodeValue;
        $msg_sign = $array_s->item(0)->nodeValue;
        $format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
        $from_xml = sprintf($format, $encrypt);

        $string = '';
        // @param $msg_sign: 签名串，对应URL参数的msg_signature
        // @param $timeStamp: 时间戳，对应URL参数的timestamp
        // @param $nonce: 随机串，对应URL参数的nonce
        // @param $from_xml: 密文，对应POST请求的数据
        // @param $string: 解密后的明文，当return返回0时有效
        // @return: 成功0，失败返回对应的错误码
        $error = $this->getWxBizMsgCrypt()->decryptMsg($msg_sign, $timeStamp, $nonce, $from_xml, $string);
        return $error ? false : $string;
    }
}