<?php

class WxDrive_Menu extends WxDrive_Console
{
    const CREATE_MENU_URL = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=';
    const GET_MENU_URL = 'https://api.weixin.qq.com/cgi-bin/menu/get?access_token=';
    const DELETE_MENU_URL = 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=';

    static $TYPE = array(
        'click' => array(
            'type' => 'click',
            'title' => '点击',
            'key' => 'key',
        ),
        'view' => array(
            'type' => 'view',
            'title' => 'url连接',
            'key' => 'url',
        ),
        'scancode_push' => array(
            'type' => 'scancode_push',
            'title' => '扫码',
            'key' => 'key',
        ),
        'scancode_waitmsg' => array(
            'type' => 'scancode_waitmsg',
            'title' => '扫码推事件且弹出“消息接收中”提示框',
            'key' => 'key',
        ),
        'pic_sysphoto' => array(
            'type' => 'pic_sysphoto',
            'title' => '弹出系统拍照发图',
            'key' => 'key',
        ),
        'pic_photo_or_album' => array(
            'type' => 'pic_photo_or_album',
            'title' => '弹出拍照或者相册发图',
            'key' => 'key',
        ),
        'pic_weixin' => array(
            'type' => 'pic_weixin',
            'title' => '弹出微信相册发图器',
            'key' => 'key',
        ),
        'location_select' => array(
            'type' => 'location_select',
            'title' => '弹出地理位置选择器',
            'key' => 'key',
        ),
        'media_id' => array(
            'type' => 'media_id',
            'title' => '下发消息（除文本消息）',
            'key' => 'media_id',
        ),
        'view_limited' => array(
            'type' => 'media_id',
            'title' => '跳转图文消息URL',
            'key' => 'media_id',
        ),
    );


    /**
     * 单例模式
     * @var $instance
     */
    static private $instance = null;

    static function getInstance()
    {
        if (self:: $instance == null) {
            self:: $instance = new self();
        }
        return self:: $instance;
    }

    /**创建菜单
     * @param $data
     * @return mixed
     */
    public function createMenu($data, $accept)
    {
        return $this->curlPost(self::CREATE_MENU_URL . $accept['access_token'], $data);
    }

    /**获取菜单
     * @return mixed
     */
    public function menuList($accept)
    {
        return $this->curlGet(self::GET_MENU_URL . $accept['access_token']);
    }

    /**删除菜单
     * @return mixed
     */
    public function menuDel($accept)
    {
        return $this->curlGet(self::DELETE_MENU_URL . $accept['access_token']);
    }

    public static function emptyMenu()
    {
        return $data = array(
            'menu' => array(
                'button' => array(
                    array(
                        'sub_button' => array(
                            array(
                                0
                            ),
                            array(
                                0
                            ),
                            array(
                                0
                            ),
                            array(
                                0
                            ),
                            array(
                                0
                            )
                        )
                    ),
                    array(
                        'sub_button' => array(
                            array(
                                0
                            ),
                            array(
                                0
                            ),
                            array(
                                0
                            ),
                            array(
                                0
                            ),
                            array(
                                0
                            )
                        )
                    ),
                    array(
                        'sub_button' => array(
                            array(
                                0
                            ),
                            array(
                                0
                            ),
                            array(
                                0
                            ),
                            array(
                                0
                            ),
                            array(
                                0
                            )
                        )
                    )
                )
            )
        );
    }


}

/*$data = array(
    "button" =>
        array(
            array(
                "type" => "click",
                "name" => "今日歌曲",
                "key" => "V1001_TODAY_MUSIC",
            ),
            array(
                "name" => "菜单",
                "sub_button" =>
                    array(
                        array(
                            "type" => "view",
                            "name" => "搜索",
                            "url" => "http://www.soso.com/",
                        ),
                        array(
                            "type" => "view",
                            "name" => "视频",
                            "url" => "http://v.qq.com/",
                        ),
                        array(
                            "type" => "click",
                            "name" => "赞一下我们",
                            "key" => "V1001_GOOD",
                        )
                    )
            )
        )
);*/

//The end of file.