<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/3
 * Time: 9:24
 */
class WxDrive_MsgRequestEvent extends WxDrive_Console
{

    /**
     * 关注/取消关注事件
     * @param $xmlstring
     * @return array|SimpleXMLElement
     */
    public function eventAttention($xmlstring)
    {
        /**
         * ToUserName    开发者微信号
         * FromUserName    发送方帐号（一个OpenID）
         * CreateTime    消息创建时间 （整型）
         * MsgType    消息类型，event
         * Event    事件类型，subscribe(订阅)、unsubscribe(取消订阅)
         */

        $data = @simplexml_load_string($xmlstring);
        if (empty($data[0])) {
            return array();
        }

        $data = array(
            'ToUserName' => self::toSting($data->ToUserName),
            'FromUserName' => self::toSting($data->FromUserName),
            'CreateTime' => self::toSting($data->CreateTime),
            'MsgType' => self::toSting($data->MsgType),
            'Event' => self::toSting($data->Event)
        );
        return $data;
    }


    /**
     * 用户未关注时，进行关注后的事件推送
     * @param $xmlstring
     * @return array|SimpleXMLElement
     */
    public function eventSubscribe($xmlstring)
    {
        /**
         * ToUserName    开发者微信号
         * FromUserName    发送方帐号（一个OpenID）
         * CreateTime    消息创建时间 （整型）
         * MsgType    消息类型，event
         * Event    事件类型，subscribe
         * EventKey    事件KEY值，qrscene_为前缀，后面为二维码的参数值
         * Ticket    二维码的ticket，可用来换取二维码图片
         */
        $data = @simplexml_load_string($xmlstring);
        if (empty($data[0])) {
            return array();
        }

        $data = array(
            'ToUserName' => self::toSting($data->ToUserName),
            'FromUserName' => self::toSting($data->FromUserName),
            'CreateTime' => self::toSting($data->CreateTime),
            'MsgType' => self::toSting($data->MsgType),
            'Event' => self::toSting($data->Event),
            'EventKey' => self::toSting($data->EventKey),
            'Ticket' => self::toSting($data->Ticket)
        );
        return $data;
    }


    /**
     * 用户已关注时的事件推送
     * @param $xmlstring
     * @return array|SimpleXMLElement
     */
    public function eventAttentionSubscribe($xmlstring)
    {
        /**
         * ToUserName    开发者微信号
         * FromUserName    发送方帐号（一个OpenID）
         * CreateTime    消息创建时间 （整型）
         * MsgType    消息类型，event
         * Event    事件类型，SCAN
         * EventKey    事件KEY值，是一个32位无符号整数，即创建二维码时的二维码scene_id
         * Ticket    二维码的ticket，可用来换取二维码图片
         */
        $data = @simplexml_load_string($xmlstring);
        if (empty($data[0])) {
            return array();
        }

        $data = array(
            'ToUserName' => self::toSting($data->ToUserName),
            'FromUserName' => self::toSting($data->FromUserName),
            'CreateTime' => self::toSting($data->CreateTime),
            'MsgType' => self::toSting($data->MsgType),
            'Event' => self::toSting($data->Event),
            'EventKey' => self::toSting($data->EventKey),
            'Ticket' => self::toSting($data->Ticket)
        );
        return $data;
    }

    /**
     * 上报地理位置事件
     * @param $xmlstring
     * @return array|SimpleXMLElement
     */
    public function eventLocation($xmlstring)
    {
        /**
         * ToUserName    开发者微信号
         * FromUserName    发送方帐号（一个OpenID）
         * CreateTime    消息创建时间 （整型）
         * MsgType    消息类型，event
         * Event    事件类型，LOCATION
         * Latitude    地理位置纬度
         * Longitude    地理位置经度
         * Precision    地理位置精度
         */
        $data = @simplexml_load_string($xmlstring);
        if (empty($data[0])) {
            return array();
        }

        $data = array(
            'ToUserName' => self::toSting($data->ToUserName),
            'FromUserName' => self::toSting($data->FromUserName),
            'CreateTime' => self::toSting($data->CreateTime),
            'MsgType' => self::toSting($data->MsgType),
            'Event' => self::toSting($data->Event),
            'Latitude' => self::toSting($data->Latitude),
            'Longitude' => self::toSting($data->Longitude),
            'Precision' => self::toSting($data->Precision)
        );
        return $data;
    }


    /**
     * 点击菜单拉取消息时的事件推送
     * @param $xmlstring
     * @return array|SimpleXMLElement
     */
    public function eventClick($xmlstring)
    {
        /**
         * ToUserName	开发者微信号
         * FromUserName	发送方帐号（一个OpenID）
         * CreateTime	消息创建时间 （整型）
         * MsgType	消息类型，event
         * Event	事件类型，CLICK
         * EventKey	事件KEY值，与自定义菜单接口中KEY值对应
         */
        $data = @simplexml_load_string($xmlstring);
        if (empty($data[0])) {
            return array();
        }

        $data = array(
            'ToUserName' => self::toSting($data->ToUserName),
            'FromUserName' => self::toSting($data->FromUserName),
            'CreateTime' => self::toSting($data->CreateTime),
            'MsgType' => self::toSting($data->MsgType),
            'Event' => self::toSting($data->Event),
            'EventKey' => self::toSting($data->EventKey)
        );
        return $data;
    }

    /**
     * 点击菜单跳转链接时的事件推送
     * @param $xmlstring
     * @return array|SimpleXMLElement
     */
    public function eventView($xmlstring)
    {
        /**
         * ToUserName	开发者微信号
         * FromUserName	发送方帐号（一个OpenID）
         * CreateTime	消息创建时间 （整型）
         * MsgType	消息类型，event
         * Event	事件类型，VIEW
         * EventKey	事件KEY值，设置的跳转URL
         */
        $data = @simplexml_load_string($xmlstring);
        if (empty($data[0])) {
            return array();
        }

        $data = array(
            'ToUserName' => self::toSting($data->ToUserName),
            'FromUserName' => self::toSting($data->FromUserName),
            'CreateTime' => self::toSting($data->CreateTime),
            'MsgType' => self::toSting($data->MsgType),
            'Event' => self::toSting($data->Event),
            'EventKey' => self::toSting($data->EventKey)
        );
        return $data;
    }


}