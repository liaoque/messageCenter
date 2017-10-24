<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/13
 * Time: 16:08
 */
class ProMessageCenter_PushData
{
    const TARGET_ALL = 'all';
    const TARGET_ONE = 'one';

    private $appId = 0;

    private $templateId = 0;

    private $ip = '';

//    目标, 'all', uid,
    private $target = 'all';

//  推送平台  "android", "ios", "winphone", "all"
    private $platform = 'all';

    private $alter = null;

//  如果指定了，则通知里原来展示 App名称的地方，将展示成这个字段。
    private $title = null;

//  这里自定义 JSON 格式的 Key/Value 信息，以供业务使用。
    private $extras = null;

//  纯粹用来作为 API 调用标识，API 返回时被原样返回，以方便 API 调用方匹配请求与返回。
    private $sendno = null;

//  推送当前用户不在线时，为该用户保留多长时间的离线消息，
//  以便其上线时再次推送。默认 86400 （1 天），最长 10 天。
//  设置为 0 表示不保留离线消息，只有推送当前在线的用户可以收到。
    private $timeToLive = null;

//  如果当前的推送要覆盖之前的一条推送，这里填写前一条推送的 msg_id 就会产生覆盖效果，即：1）
//  该 msg_id 离线收到的消息是覆盖后的内容；2）即使该 msg_id Android 端用户已经收到，如果通知栏还未清除，
//  则新的消息内容会覆盖之前这条通知；覆盖功能起作用的时限是：1 天。如果在覆盖指定时限内该 msg_id 不存在，
//  则返回 1003 错误，提示不是一次有效的消息覆盖操作，当前的消息不会被推送。
    private $overrideMsgId = null;

//  True 表示推送生产环境，False 表示要推送开发环境；如果不指定则为推送生产环境。
//  JPush 官方 API LIbrary (SDK) 默认设置为推送 “开发环境”。
    private $apnsProduction = null;

//  APNs 新通知如果匹配到当前通知中心有相同 apns-collapse-id 字段的通知，则会用新通知内容来更新它，
//  并使其置于通知中心首位。collapse id 长度不可超过 64 bytes。
    private $apnsCollapseId = null;

//  又名缓慢推送，把原本尽可能快的推送速度，降低下来，给定的n分钟内，均匀地向这次推送的目标用户推送。
//  最大值为1400.未设置则不是定速推送。
    private $bigPushDuration = null;

//  完全依赖 rom 厂商对 category 的处理策略
    private $androidCategory = null;

//  Android SDK 可设置通知栏样式，这里根据样式 ID 来指定该使用哪套样式。
    private $androidBuilderId = null;

//  默认为0，范围为 -2～2 ，其他值将会被忽略而采用默认。
    private $androidPriority = null;

//  默认为0，还有1，2，3可选，用来指定选择哪种通知栏样式，其他值无效。
//  有三种可选分别为bigText=1，Inbox=2，bigPicture=3。
    private $androidStyle = null;

//  当 style = 1 时可用，内容会被通知栏以大文本的形式展示出来。支持 api 16以上的rom。
    private $androidBigText = null;

//  当 style = 2 时可用， json 的每个 key 对应的 value 会被当作文本条目逐条展示。支持 api 16以上的rom
    private $androidInbox = null;

//  当 style = 3 时可用，可以是网络图片 url，或本地图片的 path，目前支持.jpg和.png后缀的图片。
//  图片内容会被通知栏以大图片的形式展示出来。如果是 http／https 的url，
//  会自动下载；如果要指定开发者准备的本地图片就填sdcard 的相对路径。支持 api 16以上的rom。
    private $androidBigPicPath = null;

//  如果无此字段，则此消息无声音提示；有此字段，如果找到了指定的声音就播放该声音，
//  否则播放默认声音,如果此字段为空字符串，iOS 7 为默认声音，iOS 8及以上系统为无声音。
//  (消息) 说明：JPush 官方 API Library (SDK) 会默认填充声音字段。提供另外的方法关闭声音。.
    private $iosSound = null;

//  如果不填，表示不改变角标数字；否则把角标数字改为指定的数字；为 0 表示清除。
//  JPush 官方 API Library(SDK) 会默认填充badge值为"+1",详情参考：badge +1
    private $iosBadge = null;

//  推送的时候携带"content-available":true 说明是 Background Remote Notification，
//  如果不携带此字段则是普通的Remote Notification。详情参考：Background Remote Notification
    private $iosContentAvailable = null;

//  推送的时候携带”mutable-content":true 说明是支持iOS10的UNNotificationServiceExtension，
//  如果不携带此字段则是普通的Remote Notification。详情参考：UNNotificationServiceExtension
    private $iosMutableContent = null;

//  IOS8才支持。设置APNs payload中的"category"字段值
    private $iosCategory = null;

//  点击打开的页面。会填充到推送信息的 param 字段上，表示由哪个 App 页面打开该通知。可不填，则由默认的首页打开。
    private $webPhoneOpenPage = null;

    public function __construct($info)
    {
        $this->setDatas($info);

    }

    /**
     * @return mixed
     */
    public function getAlter()
    {
        return $this->alter;
    }

    /**
     * @param mixed $alter
     */
    public function setAlter($alter)
    {
        $this->alter = $alter;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getExtras()
    {
        return $this->extras;
    }

    /**
     * @param mixed $extras
     */
    public function setExtras($extras)
    {
        $this->extras = $extras;
    }

    /**
     * @return mixed
     */
    public function getAndroidCategory()
    {
        return $this->androidCategory;
    }

    /**
     * @param mixed $androidCategory
     */
    public function setAndroidCategory($androidCategory)
    {
        $this->androidCategory = $androidCategory;
    }

    /**
     * @return mixed
     */
    public function getAndroidBuilderId()
    {
        return $this->androidBuilderId;
    }

    /**
     * @param mixed $androidBuilderId
     */
    public function setAndroidBuilderId($androidBuilderId)
    {
        $this->androidBuilderId = $androidBuilderId;
    }

    /**
     * @return mixed
     */
    public function getAndroidPriority()
    {
        return $this->androidPriority;
    }

    /**
     * @param mixed $androidPriority
     */
    public function setAndroidPriority($androidPriority)
    {
        $this->androidPriority = $androidPriority;
    }

    /**
     * @return mixed
     */
    public function getAndroidStyle()
    {
        return $this->androidStyle;
    }

    /**
     * @param mixed $androidStyle
     */
    public function setAndroidStyle($androidStyle)
    {
        $this->androidStyle = $androidStyle;
    }

    /**
     * @return mixed
     */
    public function getAndroidBigText()
    {
        return $this->androidBigText;
    }

    /**
     * @param mixed $androidBigText
     */
    public function setAndroidBigText($androidBigText)
    {
        $this->androidBigText = $androidBigText;
    }

    /**
     * @return mixed
     */
    public function getAndroidInbox()
    {
        return $this->androidInbox;
    }

    /**
     * @param mixed $androidInbox
     */
    public function setAndroidInbox($androidInbox)
    {
        $this->androidInbox = $androidInbox;
    }

    /**
     * @return mixed
     */
    public function getAndroidBigPicPath()
    {
        return $this->androidBigPicPath;
    }

    /**
     * @param mixed $androidBigPicPath
     */
    public function setAndroidBigPicPath($androidBigPicPath)
    {
        $this->androidBigPicPath = $androidBigPicPath;
    }

    /**
     * @return mixed
     */
    public function getIosSound()
    {
        return $this->iosSound;
    }

    /**
     * @param mixed $iosSound
     */
    public function setIosSound($iosSound)
    {
        $this->iosSound = $iosSound;
    }

    /**
     * @return mixed
     */
    public function getIosBadge()
    {
        return $this->iosBadge;
    }

    /**
     * @param mixed $iosBadge
     */
    public function setIosBadge($iosBadge)
    {
        $this->iosBadge = $iosBadge;
    }

    /**
     * @return mixed
     */
    public function getIosContentAvailable()
    {
        return $this->iosContentAvailable;
    }

    /**
     * @param mixed $iosContentAvailable
     */
    public function setIosContentAvailable($iosContentAvailable)
    {
        $this->iosContentAvailable = $iosContentAvailable;
    }

    /**
     * @return mixed
     */
    public function getIosMutableContent()
    {
        return $this->iosMutableContent;
    }

    /**
     * @param mixed $iosMutableContent
     */
    public function setIosMutableContent($iosMutableContent)
    {
        $this->iosMutableContent = $iosMutableContent;
    }

    /**
     * @return mixed
     */
    public function getIosCategory()
    {
        return $this->iosCategory;
    }

    /**
     * @param mixed $iosCategory
     */
    public function setIosCategory($iosCategory)
    {
        $this->iosCategory = $iosCategory;
    }

    /**
     * @return mixed
     */
    public function getWebPhoneOpenPage()
    {
        return $this->webPhoneOpenPage;
    }

    /**
     * @param mixed $webPhoneOpenPage
     */
    public function setWebPhoneOpenPage($webPhoneOpenPage)
    {
        $this->webPhoneOpenPage = $webPhoneOpenPage;
    }

    /**
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param string $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * @return string
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * @param string $platform
     */
    public function setPlatform($platform)
    {
        $this->platform = $platform;
    }

    /**
     * @return null
     */
    public function getSendno()
    {
        return $this->sendno;
    }

    /**
     * @param null $sendno
     */
    public function setSendno($sendno)
    {
        $this->sendno = $sendno;
    }

    /**
     * @return null
     */
    public function getTimeToLive()
    {
        return $this->timeToLive;
    }

    /**
     * @param null $timeToLive
     */
    public function setTimeToLive($timeToLive)
    {
        $this->timeToLive = $timeToLive;
    }

    /**
     * @return null
     */
    public function getOverrideMsgId()
    {
        return $this->overrideMsgId;
    }

    /**
     * @param null $overrideMsgId
     */
    public function setOverrideMsgId($overrideMsgId)
    {
        $this->overrideMsgId = $overrideMsgId;
    }

    /**
     * @return null
     */
    public function getApnsProduction()
    {
        return $this->apnsProduction;
    }

    /**
     * @param null $apnsProduction
     */
    public function setApnsProduction($apnsProduction)
    {
        $this->apnsProduction = $apnsProduction;
    }

    /**
     * @return null
     */
    public function getApnsCollapseId()
    {
        return $this->apnsCollapseId;
    }

    /**
     * @param null $apnsCollapseId
     */
    public function setApnsCollapseId($apnsCollapseId)
    {
        $this->apnsCollapseId = $apnsCollapseId;
    }

    /**
     * @return null
     */
    public function getBigPushDuration()
    {
        return $this->bigPushDuration;
    }

    /**
     * @param null $bigPushDuration
     */
    public function setBigPushDuration($bigPushDuration)
    {
        $this->bigPushDuration = $bigPushDuration;
    }

    /**
     * @return int
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * @param int $appId
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;
    }

    /**
     * @return int
     */
    public function getTemplateId()
    {
        return $this->templateId;
    }

    /**
     * @param int $templateId
     */
    public function setTemplateId($templateId)
    {
        $this->templateId = $templateId;
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }


    public function __toString()
    {
        // TODO: Implement __toString() method.
        $data = [];
        foreach ($this as $key => $value) {
            if ($this->$key) {
                $data[$key] = $value;
            }
        }
        return Model::enCode($data, true);
    }


    public function validation()
    {
        $templateId = $this->getTemplateId();
        return DbMessageCenter_Template::validation($templateId, DbMessageCenter_Template::TYPE_APP);
    }

    public function send()
    {
        $proMessageCenterPush = new ProMessageCenter_Push();
        return $proMessageCenterPush->addQueue($this);
    }


    public function setDatas($datas)
    {
        foreach ($datas as $key => $value) {
            $action = 'set' . ucfirst($key);
            if (method_exists($this, $action)) {
                $this->$action($value);
            }
        }
    }
}