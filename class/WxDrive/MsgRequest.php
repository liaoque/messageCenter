<?php

/**
 * 消息回调
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/1
 * Time: 16:51
 */
class WxDrive_MsgRequest extends WxDrive_Console
{


    /**
     * 文本消息
     */
    public static function textMsg($xmlstring)
    {
        /**
         * ToUserName    开发者微信号
         * FromUserName    发送方帐号（一个OpenID）
         * CreateTime    消息创建时间 （整型）
         * MsgType    text
         * Content    文本消息内容
         * MsgId    消息id，64位整型
         */
        $data = @simplexml_load_string($xmlstring);
        if (empty($data[0])) {
            return array();
        }

        $data = array(
            'URL' => self::toSting($data->URL),
            'ToUserName' => self::toSting($data->ToUserName),
            'CreateTime' => self::toSting($data->CreateTime),
            'MsgType' => self::toSting($data->MsgType),
            'Content' => self::toSting($data->Content),
            'MsgId' => self::toSting($data->MsgId)
        );
        return $data;
    }

    /**
     * 图片消息
     */
    public function imgMsg($data)
    {
        /**
         * ToUserName    开发者微信号
         * FromUserName    发送方帐号（一个OpenID）
         * CreateTime    消息创建时间 （整型）
         * MsgType    image
         * PicUrl    图片链接
         * MediaId    图片消息媒体id，可以调用多媒体文件下载接口拉取数据。
         * MsgId    消息id，64位整型
         */
        $data = array(
            'ToUserName' => self::toSting($data->ToUserName),
            'FromUserName' => self::toSting($data->FromUserName),
            'CreateTime' => self::toSting($data->CreateTime),
            'MsgType' => self::toSting($data->MsgType),
            'PicUrl' => self::toSting($data->PicUrl),
            'MediaId' => self::toSting($data->MediaId),
            'MsgId' => self::toSting($data->MsgId)
        );
        return $data;

    }

    /**
     * 语音消息
     */
    public function chatMsg($data)
    {
        /**
         * ToUserName    开发者微信号
         * FromUserName    发送方帐号（一个OpenID）
         * CreateTime    消息创建时间 （整型）
         * MsgType    语音为voice
         * MediaId    语音消息媒体id，可以调用多媒体文件下载接口拉取数据。
         * Format    语音格式，如amr，speex等
         * MsgID    消息id，64位整型
         */

        $data = array(
            'ToUserName' => self::toSting($data->ToUserName),
            'FromUserName' => self::toSting($data->FromUserName),
            'CreateTime' => self::toSting($data->CreateTime),
            'MsgType' => self::toSting($data->MsgType),
            'MediaId' => self::toSting($data->MediaId),
            'Format' => self::toSting($data->Format),
            'MsgId' => self::toSting($data->MsgId)
        );
        return $data;
    }

    /**
     * 视频消息
     */
    public function voiceMsg($data)
    {
        /**
         * ToUserName    开发者微信号
         * FromUserName    发送方帐号（一个OpenID）
         * CreateTime    消息创建时间 （整型）
         * MsgType    视频为video
         * MediaId    视频消息媒体id，可以调用多媒体文件下载接口拉取数据。
         * ThumbMediaId    视频消息缩略图的媒体id，可以调用多媒体文件下载接口拉取数据。
         * MsgId    消息id，64位整型
         */
        $data = array(
            'ToUserName' => self::toSting($data->ToUserName),
            'FromUserName' => self::toSting($data->FromUserName),
            'CreateTime' => self::toSting($data->CreateTime),
            'MsgType' => self::toSting($data->MsgType),
            'MediaId' => self::toSting($data->MediaId),
            'ThumbMediaId' => self::toSting($data->ThumbMediaId),
            'MsgId' => self::toSting($data->MsgId)
        );
        return $data;

    }

    /**
     * 小视频消息
     */
    public function shortVideoMsg($data)
    {
        /**
         * ToUserName    开发者微信号
         * FromUserName    发送方帐号（一个OpenID）
         * CreateTime    消息创建时间 （整型）
         * MsgType    小视频为shortvideo
         * MediaId    视频消息媒体id，可以调用多媒体文件下载接口拉取数据。
         * ThumbMediaId    视频消息缩略图的媒体id，可以调用多媒体文件下载接口拉取数据。
         * MsgId    消息id，64位整型
         */
        $data = array(
            'ToUserName' => self::toSting($data->ToUserName),
            'FromUserName' => self::toSting($data->FromUserName),
            'CreateTime' => self::toSting($data->CreateTime),
            'MsgType' => self::toSting($data->MsgType),
            'MediaId' => self::toSting($data->MediaId),
            'ThumbMediaId' => self::toSting($data->ThumbMediaId),
            'MsgId' => self::toSting($data->MsgId)
        );
        return $data;

    }

    /**
     * 地理位置消息
     */
    public function locationMsg($data)
    {
        /**
         * ToUserName    开发者微信号
         * FromUserName    发送方帐号（一个OpenID）
         * CreateTime    消息创建时间 （整型）
         * MsgType    location
         * Location_X    地理位置维度
         * Location_Y    地理位置经度
         * Scale    地图缩放大小
         * Label    地理位置信息
         * MsgId    消息id，64位整型
         */
        $data = array(
            'ToUserName' => self::toSting($data->ToUserName),
            'FromUserName' => self::toSting($data->FromUserName),
            'CreateTime' => self::toSting($data->CreateTime),
            'MsgType' => self::toSting($data->MsgType),
            'Location_X' => self::toSting($data->Location_X),
            'Location_Y' => self::toSting($data->Location_Y),
            'Scale' => self::toSting($data->Scale),
            'Label' => self::toSting($data->Label),
            'MsgId' => self::toSting($data->MsgId)
        );
        return $data;
    }

    /**
     * 连接消息
     */
    public function linkMsg($data)
    {
        /**
         * ToUserName    接收方微信号
         * FromUserName    发送方微信号，若为普通用户，则是一个OpenID
         * CreateTime    消息创建时间
         * MsgType    消息类型，link
         * Title    消息标题
         * Description    消息描述
         * Url    消息链接
         * MsgId    消息id，64位整型
         */
        $data = array(
            'ToUserName' => self::toSting($data->ToUserName),
            'FromUserName' => self::toSting($data->FromUserName),
            'CreateTime' => self::toSting($data->CreateTime),
            'MsgType' => self::toSting($data->MsgType),
            'Title' => self::toSting($data->Title),
            'Description' => self::toSting($data->Description),
            'Url' => self::toSting($data->Url),
            'MsgId' => self::toSting($data->MsgId)
        );
        return $data;

    }

}