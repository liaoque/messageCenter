<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/3
 * Time: 9:42
 */
class WxDrive_MsgResponse extends WxDrive_Console
{

    /**
     * 回复文本消息
     * @param $toUserName 开发者微信号
     * @param $fromUserName 发送方帐号（一个OpenID）
     * @param $content 文本消息内容
     * @return string
     */
    public static function textMsg($toUserName, $fromUserName, $content)
    {
        $responce = array(
            'ToUserName' => $toUserName,
            'FromUserName' => $fromUserName,
            'CreateTime' => time(),
            'MsgType' => 'text',
            'Content' => $content
        );
        return "<xml>
                <ToUserName><![CDATA[" . $responce['ToUserName'] . "]]></ToUserName>
                <FromUserName><![CDATA[" . $responce['FromUserName'] . "]]></FromUserName>
                <CreateTime>" . $responce['CreateTime'] . "</CreateTime>
                <MsgType><![CDATA[" . $responce['MsgType'] . "]]></MsgType>
                <Content><![CDATA[" . $responce['Content'] . "]]></Content>
                </xml>";
    }

    /**
     * 回复图片消息
     * @param $toUserName 接收方帐号（收到的OpenID）
     * @param $fromUserName 开发者微信号
     * @param $mediaId 通过素材管理接口上传多媒体文件，得到的id。
     * @return string
     */
    public function imgMsg($toUserName, $fromUserName, $mediaId)
    {
        $responce = array(
            'ToUserName' => $toUserName,
            'FromUserName' => $fromUserName,
            'CreateTime' => time(),
            'MsgType' => 'image',
            'MediaId' => $mediaId
        );
        return "<xml>
                <ToUserName><![CDATA[" . $responce['ToUserName'] . "]]></ToUserName>
                <FromUserName><![CDATA[" . $responce['FromUserName'] . "]]></FromUserName>
                <CreateTime>" . $responce['CreateTime'] . "</CreateTime>
                <MsgType><![CDATA[" . $responce['MsgType'] . "]]></MsgType>
                <Image>
                <MediaId><![CDATA[" . $responce['MediaId'] . "]]></MediaId>
                </Image>
                </xml>";

    }

    /**
     * 回复语音消息
     * @param $toUserName 是    接收方帐号（收到的OpenID）
     * @param $fromUserName 是    开发者微信号
     * @param $mediaId 通过素材管理接口上传多媒体文件，得到的id
     * @return array|string
     */
    public function chatMsg($toUserName, $fromUserName, $mediaId)
    {
        $responce = array(
            'ToUserName' => $toUserName,
            'FromUserName' => $fromUserName,
            'CreateTime' => time(),
            'MsgType' => 'voice',
            'MediaId' => $mediaId
        );
        return "<xml>
            <ToUserName><![CDATA[" . $responce['ToUserName'] . "]]></ToUserName>
            <FromUserName><![CDATA[" . $responce['FromUserName'] . "]]></FromUserName>
            <CreateTime>" . $responce['CreateTime'] . "</CreateTime>
            <MsgType><![CDATA[" . $responce['MsgType'] . "]]></MsgType>
            <Voice>
            <MediaId><![CDATA[" . $responce['MediaId'] . "]]></MediaId>
            </Voice>
            </xml>";
    }

    /**
     * 回复视频消息
     * @param $toUserName    接收方帐号（收到的OpenID）
     * @param $fromUserName    开发者微信号
     * @param $mediaId 通过素材管理接口上传多媒体文件，得到的id
     * @param $title 视频消息的标题
     * @param $description  视频消息的描述
     * @return array|string
     */
    public function voiceMsg($toUserName, $fromUserName, $mediaId, $title = null, $description = null)
    {
        $responce = array(
            'ToUserName' => $toUserName,
            'FromUserName' => $fromUserName,
            'CreateTime' => time(),
            'MsgType' => 'voice',
            'MediaId' => $mediaId,
            'Title' => $title,
            'Description' => $description
        );
        return "<xml>
                <ToUserName><![CDATA[" . $responce['ToUserName'] . "]]></ToUserName>
                <FromUserName><![CDATA[" . $responce['ToUserName'] . "]]></FromUserName>
                <CreateTime>" . $responce['CreateTime'] . "</CreateTime>
                <MsgType><![CDATA[" . $responce['MsgType'] . "]]></MsgType>
                <Video>
                <MediaId><![CDATA[" . $responce['MediaId'] . "]]></MediaId>
                <Title><![CDATA[" . $responce['Title'] . "]]></Title>
                <Description><![CDATA[" . $responce['Description'] . "]]></Description>
                </Video>
                </xml>";
    }

    /**
     * 回复音乐消息
     * @param $toUserName    接收方帐号（收到的OpenID）
     * @param $fromUserName 开发者微信号
     * @param $title        音乐标题
     * @param $description  音乐描述
     * @param $musicURL     音乐链接
     * @param $hQMusicUrl   高质量音乐链接，WIFI环境优先使用该链接播放音乐
     * @param $thumbMediaId 缩略图的媒体id，通过素材管理接口上传多媒体文件，得到的id
     * @return string
     */
    public function musicMsg($toUserName, $fromUserName, $title = null, $description = null, $musicURL = null, $hQMusicUrl = null, $thumbMediaId = null)
    {
        $responce = array(
            'ToUserName' => $toUserName,
            'FromUserName' => $fromUserName,
            'CreateTime' => time(),
            'MsgType' => 'music',
            'Title' => $title,
            'Description' => $description,
            'MusicURL' => $musicURL,
            'HQMusicUrl' => $hQMusicUrl,
            'ThumbMediaId' => $thumbMediaId
        );
        return "<xml>
                <ToUserName><![CDATA[" . $responce['ToUserName'] . "]]></ToUserName>
                <FromUserName><![CDATA[" . $responce['FromUserName'] . "]]></FromUserName>
                <CreateTime>" . $responce['CreateTime'] . "</CreateTime>
                <MsgType><![CDATA[" . $responce['MsgType'] . "]]></MsgType>
                <Music>
                <Title><![CDATA[" . $responce['Title'] . "]]></Title>
                <Description><![CDATA[" . $responce['Description'] . "]]></Description>
                <MusicUrl><![CDATA[" . $responce['MusicURL'] . "]]></MusicUrl>
                <HQMusicUrl><![CDATA[" . $responce['HQMusicUrl'] . "]]></HQMusicUrl>
                <ThumbMediaId><![CDATA[" . $responce['ThumbMediaId'] . "]]></ThumbMediaId>
                </Music>
                </xml>";
    }

    /**
     * 回复图文消息
     * @param $toUserName 是    接收方帐号（收到的OpenID）
     * @param $fromUserName 是    开发者微信号
     * @param array $articles 多条图文消息信息，默认第一个item为大图,注意，如果图文数超过10，则将会无响应
     * $articles = array(
     *      title 图文消息标题
     *      description 图文消息描述
     *      picUrl 图片链接，支持JPG、PNG格式，较好的效果为大图360*200，小图200*200
     *      url 点击图文消息跳转链接
     * );
     * @return string
     */
    public function articlesMsg($toUserName, $fromUserName, $articles = array())
    {
        if (empty($articles) || !is_array($articles)) {
            return '';
        }
        $articleCount = count($articles);
        if ($articleCount > 10) {
            return '';
        }
        $responce = array(
            'ToUserName' => $toUserName,
            'FromUserName' => $fromUserName,
            'CreateTime' => time(),
            'MsgType' => 'news',
            'ArticleCount' => $articleCount,
        );
        $item = '';
        foreach ($articles as $v) {
            $item .= "<item>
                <Title><![CDATA[" . $v['title'] . "]]></Title>
                <Description><![CDATA[" . $v['description'] . "]]></Description>
                <PicUrl><![CDATA[" . $v['picUrl'] . "]]></PicUrl>
                <Url><![CDATA[" . $v['url'] . "]]></Url>
                </item>";
        }
        return "<xml>
                <ToUserName><![CDATA[" . $responce['ToUserName'] . "]]></ToUserName>
                <FromUserName><![CDATA[" . $responce['FromUserName'] . "]]></FromUserName>
                <CreateTime>" . $responce['CreateTime'] . "</CreateTime>
                <MsgType><![CDATA[" . $responce['MsgType'] . "]]></MsgType>
                <ArticleCount>" . $responce['ArticleCount'] . "</ArticleCount>
                <Articles>
                " . $item . "
                </Articles>
                </xml> ";

    }


}