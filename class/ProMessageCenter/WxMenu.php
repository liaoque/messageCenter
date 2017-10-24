<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/8
 * Time: 13:07
 */
class ProMessageCenter_WxMenu extends ProMessageCenter_WxConsoleBase
{

    const WX_MENU = 'proWxMenu';

    public function save($menu, $accept)
    {
        WxDrive_Menu::getInstance()->createMenu($menu, $accept);
        return $this->cleanCache();
    }


    public function listMenu($accept)
    {
        $key = self::getWxMenuKey($this->getConfig()->getLocalAppId());
        $menu = Cache_File::getInstance()->get($key);
        if ($menu == -1) {
            return [];
        }
        $menu = WxDrive_Menu::getInstance()->menuList($accept);
        Cache_File::getInstance()->set($key, empty($menu) ? -1 : $menu);
        return $menu;
    }

    public function del($accept)
    {
        WxDrive_Menu::getInstance()->menuDel($accept);
        return $this->cleanCache();
    }

    public static function getWxMenuKey($localAppId)
    {
        return self::WX_MENU . $localAppId;
    }

    public function cleanCache()
    {
        $key = self::getWxMenuKey($this->getConfig()->getLocalAppId());
        Cache_File::getInstance()->del($key);
        return $this;
    }


}